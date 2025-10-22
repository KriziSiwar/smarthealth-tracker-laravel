@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .food-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }
    .action-buttons .btn {
        margin: 0 2px;
        padding: 0.25rem 0.5rem;
    }
    .stats-card {
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .sortable {
        cursor: pointer;
        position: relative;
    }
    .sortable:after {
        content: '↕';
        margin-left: 5px;
        opacity: 0.3;
    }
    .sortable.asc:after {
        content: '↑';
        opacity: 1;
    }
    .sortable.desc:after {
        content: '↓';
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête avec titre et bouton d'ajout -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-0">
                <i class="fas fa-utensils me-2"></i>Gestion des Aliments
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aliments</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.nutrition.foods.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvel Aliment
            </a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-export me-2"></i>Exporter
            </button>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Aliments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approuvés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Moyenne Calories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_calories'], 0) }} kcal</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filtres avancés</h6>
            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>
        <div class="collapse show" id="filtersCollapse">
            <div class="card-body">
                <form id="filtersForm" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom ou description...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_range" class="form-label">Date d'ajout</label>
                        <input type="text" class="form-control" id="date_range" name="date_range" value="{{ request('date_range') }}" placeholder="Sélectionnez une période">
                    </div>
                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Trier par</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
                            <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="calories" {{ request('sort_by') === 'calories' ? 'selected' : '' }}>Calories</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_order" class="form-label">Ordre</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Croissant</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.nutrition.foods.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau des aliments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Aliments</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#" id="selectAll">Tout sélectionner</a></li>
                    <li><a class="dropdown-item" href="#" id="deselectAll">Tout désélectionner</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-success" href="#" id="approveSelected"><i class="fas fa-check-circle me-2"></i>Approuver la sélection</a></li>
                    <li><a class="dropdown-item text-danger" href="#" id="deleteSelected"><i class="fas fa-trash-alt me-2"></i>Supprimer la sélection</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-file-export me-2"></i>Exporter les données
                    </a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <form id="bulkActionsForm" action="{{ route('admin.nutrition.foods.bulk-actions') }}" method="POST">
                    @csrf
                    <table class="table table-hover" id="foodsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                    </div>
                                </th>
                                <th>Nom</th>
                                <th class="sortable {{ request('sort_by') === 'calories' ? (request('sort_order') === 'asc' ? 'asc' : 'desc') : '' }}" data-sort="calories">Calories</th>
                                <th>Macronutriments</th>
                                <th>Ajouté le</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($foods as $food)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input food-checkbox" type="checkbox" name="selected_ids[]" value="{{ $food->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($food->image_path)
                                                <img src="{{ Storage::url($food->image_path) }}" alt="{{ $food->name }}" class="food-image me-3">
                                            @else
                                                <div class="food-image bg-light d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-utensils text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $food->name }}</div>
                                                <small class="text-muted">{{ Str::limit($food->description, 30) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-fire text-danger me-1"></i> {{ $food->calories }} kcal
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary" data-bs-toggle="tooltip" title="Protéines">
                                                <i class="fas fa-dumbbell me-1"></i> {{ $food->protein }}g
                                            </span>
                                            <span class="badge bg-success bg-opacity-10 text-success" data-bs-toggle="tooltip" title="Glucides">
                                                <i class="fas fa-bread-slice me-1"></i> {{ $food->carbs }}g
                                            </span>
                                            <span class="badge bg-warning bg-opacity-10 text-warning" data-bs-toggle="tooltip" title="Lipides">
                                                <i class="fas fa-oil-can me-1"></i> {{ $food->fat }}g
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $food->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $food->is_approved ? 'bg-success' : 'bg-warning' }}">
                                            <i class="fas {{ $food->is_approved ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                                            {{ $food->is_approved ? 'Approuvé' : 'En attente' }}
                                        </span>
                                        @if($food->addedBy)
                                            <div class="text-muted small mt-1">Par {{ $food->addedBy->name }}</div>
                                        @endif
                                    </td>
                                    <td class="action-buttons">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('admin.nutrition.foods.show', $food) }}" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.nutrition.foods.edit', $food) }}" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$food->is_approved)
                                                <form action="{{ route('admin.nutrition.foods.approve', $food) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.nutrition.foods.destroy', $food) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet aliment ?')"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-utensils fa-3x mb-3"></i>
                                            <p class="mb-0">Aucun aliment trouvé</p>
                                            <p class="small">Commencez par ajouter un nouvel aliment</p>
                                            <a href="{{ route('admin.nutrition.foods.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-2"></i>Ajouter un aliment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            @if($foods->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Affichage de {{ $foods->firstItem() }} à {{ $foods->lastItem() }} sur {{ $foods->total() }} aliments
                    </div>
                    <div>
                        {{ $foods->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Exporter les données</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.nutrition.foods.export') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format d'export</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exportColumns" class="form-label">Colonnes à exporter</label>
                        <select class="form-select select2" id="exportColumns" name="columns[]" multiple required>
                            <option value="name" selected>Nom</option>
                            <option value="description" selected>Description</option>
                            <option value="calories" selected>Calories</option>
                            <option value="protein" selected>Protéines (g)</option>
                            <option value="carbs" selected>Glucides (g)</option>
                            <option value="fat" selected>Lipides (g)</option>
                            <option value="is_approved" selected>Statut</option>
                            <option value="created_at" selected>Date d'ajout</option>
                        </select>
                    </div>
                    <input type="hidden" name="filters" id="exportFilters">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast de notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small>À l'instant</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialisation de Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez des options',
            allowClear: true
        });

        // Initialisation du date range picker
        $('input[name="date_range"]').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Appliquer',
                cancelLabel: 'Annuler',
                fromLabel: 'De',
                toLabel: 'À',
                customRangeLabel: 'Personnalisé',
                daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                firstDay: 1
            },
            opens: 'left',
            autoUpdateInput: false,
            @if(request('date_range'))
                startDate: '{{ explode(" - ", request("date_range"))[0] }}',
                endDate: '{{ explode(" - ", request("date_range"))[1] }}',
            @endif
            ranges: {
                'Aujourd\'hui': [moment(), moment()],
                'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 derniers jours': [moment().subtract(6, 'days'), moment()],
                '30 derniers jours': [moment().subtract(29, 'days'), moment()],
                'Ce mois-ci': [moment().startOf('month'), moment().endOf('month')],
                'Mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Tri des colonnes
        $('.sortable').on('click', function() {
            const sortBy = $(this).data('sort');
            let sortOrder = 'asc';
            
            if ($(this).hasClass('asc')) {
                sortOrder = 'desc';
            } else if ($(this).hasClass('desc')) {
                sortOrder = '';
            }
            
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            
            if (sortOrder) {
                url.searchParams.set('sort_order', sortOrder);
            } else {
                url.searchParams.delete('sort_by');
                url.searchParams.delete('sort_order');
            }
            
            window.location.href = url.toString();
        });

        // Gestion de la sélection multiple
        $('#selectAllCheckbox').on('change', function() {
            $('.food-checkbox').prop('checked', $(this).prop('checked'));
            updateSelectedCount();
        });

        $('.food-checkbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#selectAllCheckbox').prop('checked', false);
            } else {
                const allChecked = $('.food-checkbox:checked').length === $('.food-checkbox').length;
                $('#selectAllCheckbox').prop('checked', allChecked);
            }
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.food-checkbox:checked').length;
            if (count > 0) {
                $('.selected-count').remove();
                $('.card-header h6').append(`<span class="badge bg-primary ms-2 selected-count">${count} sélectionné(s)</span>`);
            } else {
                $('.selected-count').remove();
            }
        }

        // Actions groupées
        $('#selectAll').on('click', function(e) {
            e.preventDefault();
            $('.food-checkbox, #selectAllCheckbox').prop('checked', true);
            updateSelectedCount();
        });

        $('#deselectAll').on('click', function(e) {
            e.preventDefault();
            $('.food-checkbox, #selectAllCheckbox').prop('checked', false);
            updateSelectedCount();
        });

        // Approuver la sélection
        $('#approveSelected').on('click', function(e) {
            e.preventDefault();
            const selectedCount = $('.food-checkbox:checked').length;
            
            if (selectedCount === 0) {
                showToast('Sélectionnez au moins un aliment à approuver', 'warning');
                return;
            }
            
            if (confirm(`Êtes-vous sûr de vouloir approuver les ${selectedCount} aliments sélectionnés ?`)) {
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'PATCH'
                }).appendTo('#bulkActionsForm');
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'approve'
                }).appendTo('#bulkActionsForm');
                
                $('#bulkActionsForm').submit();
            }
        });

        // Supprimer la sélection
        $('#deleteSelected').on('click', function(e) {
            e.preventDefault();
            const selectedCount = $('.food-checkbox:checked').length;
            
            if (selectedCount === 0) {
                showToast('Sélectionnez au moins un aliment à supprimer', 'warning');
                return;
            }
            
            if (confirm(`Êtes-vous sûr de vouloir supprimer les ${selectedCount} aliments sélectionnés ? Cette action est irréversible.`)) {
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }).appendTo('#bulkActionsForm');
                
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'delete'
                }).appendTo('#bulkActionsForm');
                
                $('#bulkActionsForm').submit();
            }
        });

        // Exporter avec les filtres actuels
        $('#exportModal').on('show.bs.modal', function() {
            $('#exportFilters').val(JSON.stringify({
                search: '{{ request('search') }}',
                status: '{{ request('status') }}',
                date_range: '{{ request('date_range') }}',
                sort_by: '{{ request('sort_by') }}',
                sort_order: '{{ request('sort_order') }}'
            }));
        });

        // Fonction pour afficher les toasts
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('toast');
            const toastBody = toastEl.querySelector('.toast-body');
            const toastHeader = toastEl.querySelector('.toast-header strong');
            
            // Définir les classes et le texte en fonction du type
            toastEl.className = 'toast';
            toastEl.classList.add('show');
            
            switch(type) {
                case 'success':
                    toastHeader.textContent = 'Succès';
                    toastEl.querySelector('.toast-header').classList.add('bg-success', 'text-white');
                    break;
                case 'error':
                    toastHeader.textContent = 'Erreur';
                    toastEl.querySelector('.toast-header').classList.add('bg-danger', 'text-white');
                    break;
                case 'warning':
                    toastHeader.textContent = 'Attention';
                    toastEl.querySelector('.toast-header').classList.add('bg-warning', 'text-dark');
                    break;
                default:
                    toastHeader.textContent = 'Notification';
            }
            
            toastBody.textContent = message;
            
            // Masquer automatiquement après 5 secondes
            setTimeout(() => {
                const toast = bootstrap.Toast.getInstance(toastEl);
                if (toast) {
                    toast.hide();
                }
            }, 5000);
            
            // Initialiser et afficher le toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Afficher un toast si un message de succès est présent dans la session
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endpush