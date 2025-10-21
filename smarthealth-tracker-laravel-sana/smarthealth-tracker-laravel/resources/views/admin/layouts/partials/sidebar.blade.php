<!-- Sidebar -->
<div class="bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4">
        <h4>Admin Panel</h4>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ url('/admin') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
        </a>
        
        <!-- Nutrition Section -->
        <div class="dropdown">
            <a class="list-group-item list-group-item-action bg-dark text-white dropdown-toggle" href="#" role="button" id="nutritionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-utensils me-2"></i>Nutrition
            </a>
            <ul class="dropdown-menu bg-dark" aria-labelledby="nutritionDropdown">
                <li>
                    <a class="dropdown-item text-white" href="{{ route('admin.nutrition.foods.index') }}">
                        <i class="fas fa-apple-alt me-2"></i>Aliments
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white" href="{{ route('admin.nutrition.meal-plans.index') }}">
                        <i class="fas fa-clipboard-list me-2"></i>Plans de repas
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Add some CSS for the sidebar -->
<style>
    #wrapper {
        overflow-x: hidden;
    }
    
    #sidebar-wrapper {
        min-height: 100vh;
        min-width: 250px;
        max-width: 250px;
        margin-left: -250px;
        transition: margin .25s ease-out;
    }
    
    #wrapper.toggled #sidebar-wrapper {
        margin-left: 0;
    }
    
    .list-group-item {
        border: none;
        border-radius: 0;
        padding: 1rem 1.5rem;
    }
    
    .list-group-item:hover, .list-group-item:focus {
        background-color: #343a40 !important;
        color: #fff !important;
    }
    
    .dropdown-menu {
        background-color: #343a40;
        border: none;
    }
    
    .dropdown-item:hover, .dropdown-item:focus {
        background-color: #495057;
        color: #fff !important;
    }
    
    @media (min-width: 768px) {
        #sidebar-wrapper {
            margin-left: 0;
        }
        
        #wrapper.toggled #sidebar-wrapper {
            margin-left: -250px;
        }
    }
</style>
