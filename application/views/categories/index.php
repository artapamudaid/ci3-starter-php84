<div class="card shadow p-4">
  <div class="d-flex justify-content-between mb-3">
    <h5>Data Kategori</h5>
    <?php if (has_permission('categories', 'create')): ?>
			<div class="row">
				<button class="btn btn-primary btn-md" id="btnAdd"><i class="fas fa-plus"></i> Tambah Kategori</button>
				<a href="<?= site_url('categories/import') ?>" class="btn btn-success btn-md ml-2"><i class="fas fa-file-excel"></i>Import Excel</a>
			</div>
    <?php endif; ?>
  </div>

  <table id="dataTable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Kategori</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>
</div>


<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="categoryForm">
        <div class="modal-header">
          <h5 class="modal-title">Kategori</h5>
          <button type="button" class="btn-close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
       value="<?= $this->security->get_csrf_hash(); ?>">

          <input type="hidden" name="category_id">

          <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="category_name" class="form-control">
            <div class="invalid-feedback"></div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let table, modal;

$(function () {
  modal = new bootstrap.Modal(document.getElementById('categoryModal'));

  table = $("#dataTable").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "<?= site_url('categories/ajax_list') ?>",
      type: "POST",
      data: function (d) {
        d['<?= $this->security->get_csrf_token_name(); ?>'] = csrfHash;
      },
      dataSrc: function (json) {
        csrfHash = json.csrfHash;
        return json.data;
      }
    }
  });

  $("#btnAdd").click(function () {
    resetErrors($("#categoryForm"));
    $("#categoryForm")[0].reset();
    $("#categoryForm [name=category_id]").val("");
    modal.show();
  });

  $("#dataTable").on("click", ".edit", function () {
    let category_id = $(this).data("id");
    $.getJSON("<?= site_url('categories/ajax_edit/') ?>" + category_id, function (res) {
      csrfHash = res.csrfHash;
      resetErrors($("#categoryForm"));
      $("#categoryForm [name=category_id]").val(res.data.category_id);
      $("#categoryForm [name=category_name]").val(res.data.category_name);
      modal.show();
    });
  });

  $("#categoryForm").submit(function (e) {
    e.preventDefault();
    let form = $(this);
    $.post("<?= site_url('categories/ajax_save') ?>", form.serialize(), function (res) {
      csrfHash = res.csrfHash;
      resetErrors(form);
      if (res.status === "error") {
        showErrors(form, res.errors);
      } else {
        modal.hide();
        notify("success", res.message);
        table.ajax.reload(null, false);
      }
    }, 'json');
  });

  $("#dataTable").on("click", ".delete", function () {
    let category_id = $(this).data("id");
    Swal.fire({
      title: "Hapus kategori?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
			cancelButtonText: "Batal"
    }).then((r) => {
      if (r.isConfirmed) {
        $.post("<?= site_url('categories/ajax_delete/') ?>" + category_id, {
          [csrfName]: csrfHash
        }, function (res) {
          csrfHash = res.csrfHash;
          if (res.status === "success") {
            notify("success", res.message);
            table.ajax.reload(null, false);
          }
        }, 'json');
      }
    });
  });
});
</script>
