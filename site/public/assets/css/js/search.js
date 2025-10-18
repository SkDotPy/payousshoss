const SEARCH_API_URL = '/backend/search/animals.php';
const DEBOUNCE_DELAY = 500;

const searchCache = new Map();
let currentSearchParams = {};

document.addEventListener('DOMContentLoaded', function() {
    initSearchListeners();
    performInitialSearch();
});

function initSearchListeners() {
    const searchInput = document.getElementById('searchInput');
    const filterSpecies = document.getElementById('filterSpecies');
    const filterSex = document.getElementById('filterSex');
    const filterAge = document.getElementById('filterAge');
    const filterRace = document.getElementById('filterRace');
    const filterColor = document.getElementById('filterColor');
    const sortBy = document.getElementById('sortBy');
    const resetBtn = document.getElementById('resetFilters');
    const clearBtn = document.getElementById('clearSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', PawConnect.debounce(performSearch, DEBOUNCE_DELAY));
    }
    
    if (filterSpecies) filterSpecies.addEventListener('change', performSearch);
    if (filterSex) filterSex.addEventListener('change', performSearch);
    if (filterAge) filterAge.addEventListener('change', performSearch);
    if (sortBy) sortBy.addEventListener('change', performSearch);
    
    if (filterRace) {
        filterRace.addEventListener('input', PawConnect.debounce(performSearch, DEBOUNCE_DELAY));
    }
    if (filterColor) {
        filterColor.addEventListener('input', PawConnect.debounce(performSearch, DEBOUNCE_DELAY));
    }
    
    if (clearBtn) {
        clearBtn.addEventListener('click', clearSearch);
    }
    if (resetBtn) {
        resetBtn.addEventListener('click', resetAllFilters);
    }
}

async function performInitialSearch() {
    await performSearch();
}

async function performSearch() {
    const params = getSearchParams();
    
    const cacheKey = JSON.stringify(params);
    if (searchCache.has(cacheKey)) {
        displayResults(searchCache.get(cacheKey));
        return;
    }
    
    showLoader();
    
    try {
        const url = new URL(SEARCH_API_URL, window.location.origin);
        Object.keys(params).forEach(key => {
            if (params[key]) url.searchParams.append(key, params[key]);
        });
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        searchCache.set(cacheKey, data);
        
        if (data.success) {
            displayResults(data);
        } else {
            showError(data.message || 'Erreur lors de la recherche');
        }
        
    } catch (error) {
        console.error('Erreur de recherche:', error);
        showError('Une erreur est survenue lors de la recherche');
    }
}

function getSearchParams() {
    const params = {
        q: document.getElementById('searchInput')?.value.trim() || '',
        species: document.getElementById('filterSpecies')?.value || '',
        sex: document.getElementById('filterSex')?.value || '',
        age: document.getElementById('filterAge')?.value || '',
        race: document.getElementById('filterRace')?.value.trim() || '',
        color: document.getElementById('filterColor')?.value.trim() || '',
        sort: document.getElementById('sortBy')?.value || 'recent'
    };
    
    currentSearchParams = params;
    return params;
}

function showLoader() {
    const loader = document.getElementById('loadingIndicator');
    const results = document.getElementById('resultsGrid');
    const noResults = document.getElementById('noResults');
    
    if (loader) loader.style.display = 'block';
    if (results) results.style.display = 'none';
    if (noResults) noResults.style.display = 'none';
}

function displayResults(data) {
    const loader = document.getElementById('loadingIndicator');
    const resultsGrid = document.getElementById('resultsGrid');
    const noResults = document.getElementById('noResults');
    const resultsCount = document.getElementById('resultsCount');
    
    if (loader) loader.style.display = 'none';
    
    if (resultsCount) {
        const count = data.animals?.length || 0;
        resultsCount.textContent = count === 0 
            ? 'Aucun résultat' 
            : `${count} résultat${count > 1 ? 's' : ''} trouvé${count > 1 ? 's' : ''}`;
    }
    
    if (!data.animals || data.animals.length === 0) {
        if (resultsGrid) resultsGrid.style.display = 'none';
        if (noResults) noResults.style.display = 'block';
        return;
    }
    
    if (noResults) noResults.style.display = 'none';
    if (resultsGrid) {
        resultsGrid.style.display = 'flex';
        resultsGrid.innerHTML = '';
        
        data.animals.forEach(animal => {
            const card = createAnimalCard(animal);
            resultsGrid.appendChild(card);
        });
    }
}

function createAnimalCard(animal) {
    const col = document.createElement('div');
    col.className = 'col-md-4 col-lg-3';
    
    const speciesIcons = {
        'chien': 'fa-dog',
        'chat': 'fa-cat',
        'lapin': 'fa-rabbit',
        'oiseau': 'fa-dove',
        'autre': 'fa-paw'
    };
    
    const icon = speciesIcons[animal.species?.toLowerCase()] || 'fa-paw';
    
    const stateBadge = animal.state === 'disponible' 
        ? '<span class="badge-available">Disponible</span>'
        : animal.state === 'adopte' 
        ? '<span class="badge-available" style="background:#EF4444">Adopté</span>'
        : '<span class="badge-available" style="background:#F59E0B">En attente</span>';
    
    const imageSrc = animal.image || '/assets/images/animals/placeholder.jpg';
    
    col.innerHTML = `
        <div class="card card-animal h-100">
            <img src="${imageSrc}" 
                 alt="${animal.nom}" 
                 class="card-img-top"
                 onerror="this.src='/assets/images/animals/placeholder.jpg'">
            ${stateBadge}
            <div class="card-body">
                <h5 class="card-title">${animal.nom || 'Sans nom'}</h5>
                <p class="text-muted mb-2">
                    <i class="fas ${icon}"></i> 
                    ${animal.race || 'Race inconnue'} • 
                    ${animal.age || '?'} an${animal.age > 1 ? 's' : ''} • 
                    ${animal.sex === 'male' ? 'Mâle' : 'Femelle'}
                </p>
                <p class="card-text">${truncateText(animal.description || 'Adorable compagnon à adopter', 80)}</p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-muted small">
                        <i class="fas fa-map-marker-alt"></i> 
                        ${animal.location || 'Non spécifié'}
                    </span>
                    <a href="animal-detail.php?id=${animal.id}" class="btn btn-primary btn-sm">
                        Voir plus
                    </a>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
}

function showError(message) {
    const loader = document.getElementById('loadingIndicator');
    const resultsGrid = document.getElementById('resultsGrid');
    const noResults = document.getElementById('noResults');
    
    if (loader) loader.style.display = 'none';
    if (resultsGrid) resultsGrid.style.display = 'none';
    if (noResults) {
        noResults.style.display = 'block';
        noResults.innerHTML = `
            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
            <h4>Erreur</h4>
            <p class="text-muted">${message}</p>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-redo me-2"></i> Réessayer
            </button>
        `;
    }
    
    PawConnect.showToast(message, 'error');
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        performSearch();
    }
}

function resetAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterSpecies').value = '';
    document.getElementById('filterSex').value = '';
    document.getElementById('filterAge').value = '';
    document.getElementById('filterRace').value = '';
    document.getElementById('filterColor').value = '';
    document.getElementById('sortBy').value = 'recent';
    
    searchCache.clear();
    
    performSearch();
    
    PawConnect.showToast('Filtres réinitialisés', 'info');
}

window.SearchModule = {
    performSearch,
    clearSearch,
    resetAllFilters
};