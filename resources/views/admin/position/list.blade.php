@extends('adminlte::page')

@section('title', $title ?? 'Positions')

@section('content_header')@stop

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start pt-4">
    <div class="align-self-end">
        <h3 class="page-title mb-0">{{ $title ?? 'Positions' }}</h3>
    </div>
    <button class="btn btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#create-position-modal">
        <i class="bi bi-plus-lg me-1"></i>Add Position
    </button>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="pt-3"></div>

<!-- Data Table Card -->
<div class="card pt-3">
    <div class="card-body p-0">
        <table id="positions" class="table table-hover mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="create-position-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="position-form">
                    <input type="hidden" id="position-id">
                    <div class="form-group-modal">
                        <label for="name">Name <span style="color: var(--danger);">*</span></label>
                        <input class="form-control" type="text" id="name" name="name" placeholder="Enter name" required>
                        <div id="nameError" class="error-message"></div>
                    </div>
                    <div class="form-group-modal">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Enter description (optional)"></textarea>
                        <div id="descriptionError" class="error-message"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-position-button">
                    <i class="bi bi-check-lg me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="view-position-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Position Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Name</div>
                    <div class="detail-value" id="view-name">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Description</div>
                    <div class="detail-value" id="view-description">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Created</div>
                    <div class="detail-value" id="view-created-at">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete-position-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this position? This action cannot be undone.</p>
                <input type="hidden" id="delete-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-button">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
var table, url = "";

$(document).ready(function() {
    // Initialize DataTable using SBR utility with createdRow callback
    table = SBR.dataTable.init('positions', {
        ajax: {
            url: "{{ route('positions.datatable') }}",
            type: 'GET',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'created_at', name: 'created_at' },
            { data: null, name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        columnDefs: [
            {
                targets: 1, // Description column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderTruncated(data, 40);
                    }
                    return data;
                }
            },
            {
                targets: 2, // Created At column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderDate(data);
                    }
                    return data;
                }
            },
            {
                targets: 3, // Actions column
                render: function(data, type, row) {
                    return SBR.dataTable.renderActions(row.id, ['view', 'edit', 'delete']);
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Add data-id attribute to row for easy access
            $(row).attr('data-id', data.id);
        },
        order: [[2, 'desc']]
    });

    // Save Position
    $('#save-position-button').click(function() {
        const id = $('#position-id').val();
        const data = SBR.form.getData('position-form');

        SBR.form.clearErrors('position-form');
        SBR.ui.toggleLoading('save-position-button', true);

        const url = id
            ? `/positions/${id}`
            : '/positions';

        const operation = id ? SBR.crud.update : SBR.crud.create;

        operation(
            url,
            data,
            function(response) {
                SBR.ui.toggleLoading('save-position-button', false);
                SBR.modal.hide('create-position-modal');
                SBR.dataTable.reload(table);
                SBR.form.reset('position-form');
                SBR.alert.success(response.message || 'Position saved successfully');
            },
            function(errors) {
                SBR.ui.toggleLoading('save-position-button', false);
                if (typeof errors === 'object') {
                    SBR.form.showErrors('position-form', errors);
                } else {
                    SBR.alert.error(errors);
                }
            }
        );
    });

    // View Position
    $(document).on('click', '.view-btn', function() {
        SBR.crud.get(
            `/positions/${$(this).data('id')}`,
            function(data) {
                $('#view-name').text(data.name);
                $('#view-description').text(data.description || 'No description');
                $('#view-created-at').text(SBR.dataTable.renderDateTime(data.created_at) || '-');
                SBR.modal.show('view-position-modal');
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Edit Position
    $(document).on('click', '.edit-btn', function() {
        SBR.crud.get(
            `/positions/${$(this).data('id')}`,
            function(data) {
                var modal = $('#create-position-modal');
                modal.find('#position-id').val(data.id);
                modal.find('#name').val(data.name);
                modal.find('#description').val(data.description);
                modal.find('.modal-title').text('Edit Position');
                SBR.modal.show(modal.attr('id'));
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Delete Position - Show
    $(document).on('click', '.delete-btn', function() {
        $('#delete-id').val($(this).data('id'));
        SBR.modal.show('delete-position-modal');
    });

    // Delete Position - Confirm
    $('#confirm-delete-button').click(function() {
        SBR.ui.toggleLoading('confirm-delete-button', true);
        SBR.crud.delete(
            `/positions/${$('#delete-id').val()}`,
            function(response) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-position-modal');
                SBR.dataTable.reload(table);
                SBR.alert.success(response.message || 'Position deleted successfully');
            },
            function(message) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-position-modal');
                SBR.alert.error(message);
            }
        );
    });

    // Reset form when modal is closed
    document.getElementById('create-position-modal').addEventListener('hidden.bs.modal', function() {
        var modal = $(this);
        SBR.form.reset(modal.find('form').attr('id'));
        modal.find('#position-id').val('');
        modal.find('.modal-title').text('Add Position');
    });
});
</script>
@endsection