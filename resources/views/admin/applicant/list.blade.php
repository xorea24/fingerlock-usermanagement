@extends('adminlte::page')

@section('title', $title ?? 'Applicants')

@section('content_header')@stop

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start pt-4">
    <div class="align-self-end">
        <h3 class="page-title mb-0">{{ $title ?? 'Applicants' }}</h3>
    </div>
    <button class="btn btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#create-applicant-modal">
        <i class="bi bi-plus-lg me-1"></i>Add Applicant
    </button>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="pt-3"></div>

<!-- Data Table Card -->
<div class="card pt-3">
    <div class="card-body p-0">
        <table id="applicants" class="table table-hover mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Civil Status</th>
                    <th>Address</th>
                    <th>Position</th>
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
<div class="modal fade" id="create-applicant-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Applicant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="applicant-form">
                    <input type="hidden" id="applicant-id">
                    <div class="row">
                        <div class="form-group-modal col-md-3">
                            <label for="first-name">First Name <span style="color: var(--danger);">*</span></label>
                            <input class="form-control text-uppercase" type="text" id="first-name" name="first_name" placeholder="Enter first name" required>
                            <div id="first-nameError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="middle-name">Middle Name</label>
                            <input class="text-uppercase" type="text" id="middle-name" name="middle_name" placeholder="Enter middle name">
                            <div id="middle-nameError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="last-name">Last Name <span style="color: var(--danger);">*</span></label>
                            <input class="form-control text-uppercase" type="text" id="last-name" name="last_name" placeholder="Enter last name" required>
                            <div id="last-nameError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="extension-name">Extension Name</label>
                            <input class="text-uppercase" type="text" id="extension-name" name="extension_name" placeholder="Enter extension name">
                            <div id="extension-nameError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group-modal col-md-3">
                            <label for="birthdate">Birthdate <span style="color: var(--danger);">*</span></label>
                            <input class="form-control" type="date" id="birthdate" name="birthdate" required>
                            <div id="birthdateError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="gender">Gender <span style="color: var(--danger);">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select...</option>
                                @foreach($genders as $gender)
                                    <option value="{{ $gender }}">{{ $gender }}</option>
                                @endforeach
                            </select>
                            <div id="genderError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="civil-status">Civil Status <span style="color: var(--danger);">*</span></label>
                            <select class="form-select" id="civil-status" name="civil_status" required>
                                <option value="">Select...</option>
                                @foreach($civil_statuses as $civil_status)
                                    <option value="{{ $civil_status }}">{{ $civil_status }}</option>
                                @endforeach
                            </select>
                            <div id="civil-statusError" class="error-message"></div>
                        </div>
                        <div class="form-group-modal col-md-3">
                            <label for="position-id">Position <span style="color: var(--danger);">*</span></label>
                            <select class="form-select" id="position-id" name="position_id" required>
                                <option value="">Select...</option>
                            </select>
                            <div id="position-idError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="form-group-modal">
                        <label for="address">Address <span style="color: var(--danger);">*</span></label>
                        <textarea class="form-control text-uppercase" id="address" name="address" placeholder="Enter address" required></textarea>
                        <div id="addressError" class="error-message"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-applicant-button">
                    <i class="bi bi-check-lg me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="view-applicant-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Applicant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Name</div>
                    <div class="detail-value" id="view-name">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Gender</div> 
                    <div class="detail-value" id="view-gender">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Birthdate</div> 
                    <div class="detail-value" id="view-birthdate">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Civil Status</div> 
                    <div class="detail-value" id="view-civil-status">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Address</div> 
                    <div class="detail-value" id="view-address">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Position</div> 
                    <div class="detail-value" id="view-position">-</div>
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
<div class="modal fade" id="delete-applicant-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this applicant? This action cannot be undone.</p>
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
var table, createApplicantModal = document.getElementById('create-applicant-modal');

$(document).ready(function() {

    var positionIdSelect = $('#position-id').select2({
        dropdownParent: createApplicantModal,
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
    table = SBR.dataTable.init('applicants', {
        ajax: {
            url: "{{ route('applicants.datatable') }}",
            type: 'GET',
        },
        columns: [
            { data: 'full_name', name: 'name' },
            { data: 'gender', name: 'gender' },
            { data: 'birthdate', name: 'birthdate' },
            { data: 'civil_status', name: 'civil_status' },
            { data: 'address', name: 'address' },
            { data: 'position.name', name: 'position' },
            { data: 'created_at', name: 'created_at' },
            { data: null, name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        columnDefs: [
            {
                targets: 4, // Description column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderTruncated(data, 40);
                    }
                    return data;
                }
            },
            {
                targets: [2, 6], // Birthdate and Created At column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return SBR.dataTable.renderDate(data);
                    }
                    return data;
                }
            },
            {
                targets: 7, // Actions column
                render: function(data, type, row) {
                    return SBR.dataTable.renderActions(row.id, ['view', 'edit', 'delete']);
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Add data-id attribute to row for easy access
            $(row).attr('data-id', data.id);
        },
        order: [[6, 'desc']]
    });

    // Save Applicant
    $('#save-applicant-button').click(function() {
        const id = $('#applicant-id').val();
        const data = SBR.form.getData('applicant-form');

        SBR.form.clearErrors('applicant-form');
        SBR.ui.toggleLoading('save-applicant-button', true);

        const url = id
            ? `/applicants/${id}`
            : '/applicants';

        const operation = id ? SBR.crud.update : SBR.crud.create;

        operation(
            url,
            data,
            function(response) {
                SBR.ui.toggleLoading('save-applicant-button', false);
                SBR.modal.hide('create-applicant-modal');
                SBR.dataTable.reload(table);
                SBR.form.reset('applicant-form');
                SBR.alert.success(response.message || 'Applicant saved successfully');
            },
            function(errors) {
                SBR.ui.toggleLoading('save-applicant-button', false);
                if (typeof errors === 'object') {
                    SBR.form.showErrors('applicant-form', errors);
                } else {
                    SBR.alert.error(errors);
                }
            }
        );
    });

    // View Applicant
    $(document).on('click', '.view-btn', function() {
        SBR.crud.get(
            `/applicants/${$(this).data('id')}`,
            function(data) {
                $('#view-name').text(data.full_name);
                $('#view-gender').text(data.gender);
                $('#view-birthdate').text(SBR.dataTable.renderDate(data.birthdate));
                $('#view-civil-status').text(data.civil_status);
                $('#view-position').text(data.position.name);
                $('#view-address').text(data.address);
                $('#view-created-at').text(SBR.dataTable.renderDateTime(data.created_at) || '-');
                SBR.modal.show('view-applicant-modal');
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Edit Applicant
    $(document).on('click', '.edit-btn', function() {
        SBR.crud.get(
            `/applicants/${$(this).data('id')}`,
            function(data) {
                var modal = $('#create-applicant-modal');
                modal.find('#applicant-id').val(data.id);
                modal.find('#first-name').val(data.first_name);
                modal.find('#middle-name').val(data.middle_name);
                modal.find('#last-name').val(data.last_name);
                modal.find('#extension-name').val(data.extension_name);
                modal.find('#gender').val(data.gender);
                modal.find('#birthdate').val(data.birthdate);
                modal.find('#civil-status').val(data.civil_status);
                modal.find('#address').val(data.address);
                positionIdSelect
                    .append(new Option(data.position.name, data.position.id, false, false))
                    .val(data.position.id)
                    .trigger('change');
                modal.find('.modal-title').text('Edit Applicant');
                SBR.modal.show(modal.attr('id'));
            },
            function(message) {
                SBR.alert.error(message);
            }
        );
    });

    // Delete Applicant - Show
    $(document).on('click', '.delete-btn', function() {
        $('#delete-id').val($(this).data('id'));
        SBR.modal.show('delete-applicant-modal');
    });

    // Delete Applicant - Confirm
    $('#confirm-delete-button').click(function() {
        SBR.ui.toggleLoading('confirm-delete-button', true);
        SBR.crud.delete(
            `/applicants/${$('#delete-id').val()}`,
            function(response) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-applicant-modal');
                SBR.dataTable.reload(table);
                SBR.alert.success(response.message || 'Applicant deleted successfully');
            },
            function(message) {
                SBR.ui.toggleLoading('confirm-delete-button', false);
                SBR.modal.hide('delete-applicant-modal');
                SBR.alert.error(message);
            }
        );
    });

    // Reset form when modal is closed
    createApplicantModal.addEventListener('hidden.bs.modal', function() {
        var modal = $(this);
        SBR.form.reset(modal.find('form').attr('id'));
        modal.find('#applicant-id').val('');
        modal.find('.modal-title').text('Add Applicant');
    });
});
</script>
@endsection