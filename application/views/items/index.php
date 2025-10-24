<div class="card shadow p-4">
  <div class="d-flex justify-content-between mb-3">
    <h5>Data Barang</h5>
    <?php if (has_permission('items', 'create')): ?>
			<div class="row">
				<button class="btn btn-primary btn-md" id="btnAdd"><i class="fas fa-plus"></i> Tambah Barang</button>
				<a href="<?= site_url('items/import') ?>" class="btn btn-success btn-md ml-2"><i class="fas fa-file-excel"></i>Import Excel</a>
			</div>
    <?php endif; ?>
  </div>

  <table id="dataTable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Deskripsi</th>
        <th>Unit</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>
</div>


<!-- Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="itemForm">
        <div class="modal-header">
          <h5 class="modal-title">Barang</h5>
          <button type="button" class="btn-close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
       value="<?= $this->security->get_csrf_hash(); ?>">

          <input type="hidden" name="item_id">

          <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="item_code" class="form-control">
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="name" class="form-control">
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control"></textarea>
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label>Unit</label>
            <select name="unit" class="form-control">
              <option value="kg">Kg</option>
              <option value="liter">Liter</option>
              <option value="botol">Botol</option>
              <option value="pack">Pack</option>
              <option value="box">Box</option>
            </select>
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
  modal = new bootstrap.Modal(document.getElementById('itemModal'));

  table = $("#dataTable").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "<?= site_url('items/ajax_list') ?>",
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
    resetErrors($("#itemForm"));
    $("#itemForm")[0].reset();
    $("#itemForm [name=item_id]").val("");
    $("#itemForm [name=unit]")[0].selectedIndex = 0;
    modal.show();
  });

  $("#dataTable").on("click", ".edit", function () {
    let item_id = $(this).data("id");
    $.getJSON("<?= site_url('items/ajax_edit/') ?>" + item_id, function (res) {
      csrfHash = res.csrfHash;
      resetErrors($("#itemForm"));
      $("#itemForm [name=item_id]").val(res.data.item_id);
      $("#itemForm [name=item_code]").val(res.data.item_code);
      $("#itemForm [name=name]").val(res.data.name);
      $("#itemForm [name=description]").val(res.data.description);
      $("#itemForm [name=unit]").val(res.data.unit || '');
      modal.show();
    });
  });

  $("#itemForm").submit(function (e) {
    e.preventDefault();
    let form = $(this);
    $.post("<?= site_url('items/ajax_save') ?>", form.serialize(), function (res) {
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
    let item_id = $(this).data("id");
    Swal.fire({
      title: "Hapus barang?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
			cancelButtonText: "Batal"
    }).then((r) => {
      if (r.isConfirmed) {
        $.post("<?= site_url('items/ajax_delete/') ?>" + item_id, {
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
