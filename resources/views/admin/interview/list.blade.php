@extends('adminlte::page')

@section('title', $title ?? 'Interviews')

@section('content_header')@stop

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start pt-4">
    <div class="align-self-end">
        <h3 class="page-title mb-0">{{ $title ?? 'Interviews' }}</h3>
    </div>
    <button class="btn btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#create-interview-modal">
        <i class="bi bi-plus-lg me-1"></i>Add Interview
    </button>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="pt-3"></div>

<!-- Data Table Card -->
<div class="card pt-3">
    <div class="card-body p-0">
        <table id="interviews" class="table table-hover mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Interview Date</th>
                    <th>Position</th>
                    <th>Status</th>
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
<div class="modal fade" id="create-interview-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Interview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="interview-form">
                    <input type="hidden" id="interview-id">
                    <div class="form-group-modal">
                        <label for="interview-date">Date <span style="color: var(--danger);">*</span></label>
                        <input class="form-control" type="datetime-local" id="interview-date" name="interview_date" required>
                        <div id="interview-dateError" class="error-message"></div>
                    </div>
                    <div class="form-group-modal">
                        <label for="position-id">Position <span style="color: var(--danger);">*</span></label>
                        <select class="form-select" id="position-id" name="position_id" required>
                            <option value="">Select...</option>
                        </select>
                        <div id="position-idError" class="error-message"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-interview-button">
                    <i class="bi bi-check-lg me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="view-interview-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Interview Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Interview Date</div>
                    <div class="detail-value" id="view-interview-date">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Position</div>
                    <div class="detail-value" id="view-position">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value" id="view-status">-</div>
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
<div class="modal fade" id="delete-interview-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this interview? This action cannot be undone.</p>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var table, createInterviewModal = document.getElementById('create-interview-modal');

$(document).ready(function() {

    var positionIdSelect = $('#position-id').select2({
        dropdownParent: createInterviewModal,
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search...',
        allowClear: true,
        ajax: {
            url: "{{ route('positions.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { search: params.term };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    // Initialize DataTable using SBR utility with createdRow callback
    table = SBR.dataTable.init('interviews', {
        ajax: {
            url: "{{ route('interviews.datatable') }}",
            type: 'GET',
        },
        columns: [
            { data: 'interview_date', name: 'interview_date' },
            { data: 'position.name', name: 'position' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: null, name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        columnDefs: [
            {
                targets: 0, // Interview Date column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderDateTime(data);
                    }
                    return data;
                }
            },
            {
                targets: 2,
                render: function(data, type, row) {
                    const badgeClass = {
                        'WAITING': 'bg-primary',
                        'ONGOING': 'bg-warning',
                        'POSTPONED': 'bg-secondary',
                        'CANCELLED': 'bg-danger',
                        'COMPLETED': 'bg-success',
                    };
                    return `<span class="badge ${badgeClass[data]}">${data}</span>`;
                }
            },
            {
                targets: 3, // Created At column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderDate(data);
                    }
                    return data;
                }
            },
            {
                targets: 4, // Actions column
                render: function(data, type, row) {
                    return SBR.dataTable.renderActions(row.id, ['view', 'edit', 'delete']);
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Add data-id attribute to row for easy access
            $(row).attr('data-id', data.id);
        },
        order: [[0, 'desc']]
    });

    // Save Interview
    $('#save-interview-button').click(function() {
        const id = $('#interview-id').val();
        const data = SBR.form.getData('interview-form');

        SBR.form.clearErrors('interview-form');
        SBR.ui.toggleLoading('save-interview-button', true);

        const url = id
            ? `/interviews/${id}`
            : '/interviews';

        const operation = id ? SBR.crud.update : SBR.crud.create;

        operation(
            url,
            data,
            function(response) {
                SBR.ui.toggleLoading('save-interview-button', false);
                SBR.modal.hide('create-interview-modal');
                SBR.dataTable.reload(table);
                SBR.form.reset('interview-form');
                SBR.alert.success(response.message || 'Interview saved successfully');
            },
            function(errors) {
                SBR.ui.toggleLoading('save-interview-button', false);
                if (typeof errors === 'object') {
                    SBR.form.showErrors('interview-form', errors);
                } else {
                    SBR.alert.error(errors);
                }
            }
        );
    });

    // View Interview
    $(document).on('click', '.view-btn', function() {
        SBR.crud.get(
            `/interviews/${$(this).data('id')}`,
            function(data) {
                $('#view-interview-date').text(SBR.dataTable.renderDateTime(data.interview_date));
                $('#view-position').text(data.position.name);
                $('#view-status').text(data.status);
                $('#view-created-at').text(SBR.dataTable.renderDateTime(data.created_at) || '-');
                SBR.modal.show('view-interview-modal');
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Edit Interview
    $(document).on('click', '.edit-btn', function() {
        SBR.crud.get(
            `/interviews/${$(this).data('id')}`,
            function(data) {
                var modal = $('#create-interview-modal');
                modal.find('#interview-id').val(data.id);
                modal.find('#interview-date').val(data.interview_date);
                positionIdSelect
                    .append(new Option(data.position.name, data.position.id, false, false))
                    .val(data.position.id)
                    .trigger('change');
                modal.find('.modal-title').text('Edit Interview');
                SBR.modal.show(modal.attr('id'));
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Delete Interview - Show
    $(document).on('click', '.delete-btn', function() {
        $('#delete-id').val($(this).data('id'));
        SBR.modal.show('delete-interview-modal');
    });

    // Delete Interview - Confirm
    $('#confirm-delete-button').click(function() {
        SBR.ui.toggleLoading('confirm-delete-button', true);
        SBR.crud.delete(
            `/interviews/${$('#delete-id').val()}`,
            function(response) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-interview-modal');
                SBR.dataTable.reload(table);
                SBR.alert.success(response.message || 'Interview deleted successfully');
            },
            function(message) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-interview-modal');
                SBR.alert.error(message);
            }
        );
    });

    // Reset form when modal is closed
    createInterviewModal.addEventListener('hidden.bs.modal', function() {
        var modal = $(this);
        SBR.form.reset(modal.find('form').attr('id'));
        modal.find('#interview-id').val('');
        modal.find('.modal-title').text('Add Interview');
    });
});
</script>
@endsection