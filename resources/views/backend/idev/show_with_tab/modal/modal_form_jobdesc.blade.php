<div class="modal fade" id="jobdescModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Tambah Jobdesc
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form id="jobdescForm" enctype="multipart/form-data">

                @csrf

                {{-- kosong = create, ada id = update --}}
                <input type="hidden" name="id" id="jobdesc_id">

                {{-- file lama saat edit --}}
                <input type="hidden" name="old_file" id="jobdesc_old_file">

                <input type="hidden"
                       name="section_id"
                       value="{{ $detail->id ?? '' }}">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Name
                        </label>

                        <input type="text"
                               name="name"
                               id="jobdesc_name"
                               class="form-control"
                               required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">
                            File PDF
                        </label>

                        <input type="file"
                               name="file"
                               id="jobdesc_file"
                               class="form-control"
                               accept=".pdf">

                        <small class="text-muted">
                            Kosongkan jika tidak ingin mengganti file.
                        </small>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="is_active"
                                id="jobdesc_is_active"
                                class="form-control">

                            <option value="1">
                                Active
                            </option>

                            <option value="0"
                                    selected>
                                Inactive
                            </option>

                        </select>
                    </div>


                </div>


                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Simpan
                    </button>

                </div>


            </form>

        </div>
    </div>
</div>
