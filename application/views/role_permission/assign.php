<div class="card shadow p-4">
  <div class="d-flex justify-content-between mb-3">
    <h5>Assign Access for: <?= html_escape($role->name) ?></h5>
    <?php if($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
  </div>
  <form method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
           value="<?= $this->security->get_csrf_hash(); ?>">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Modul</th>
          <th>
            Akses <br>
            <input type="checkbox" id="checkAllRead">
          </th>
          <th>
            Tambah <br>
            <input type="checkbox" id="checkAllCreate">
          </th>
          <th>
            Edit <br>
            <input type="checkbox" id="checkAllUpdate">
          </th>
          <th>
            Hapus <br>
            <input type="checkbox" id="checkAllDelete">
          </th>
          <th>
			Bisa Semua <br>
            <input type="checkbox" id="check-all-global"> 
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($menus as $m): 
          $e = $existing[$m->id] ?? null;
        ?>
        <tr>
          <td><?= html_escape($m->title) ?> </td>
          <td class="text-center">
            <input type="checkbox" name="read_<?= $m->id ?>" class="check-item read" <?= $e && $e->can_read ? 'checked':'' ?>>
          </td>
          <td class="text-center">
            <input type="checkbox" name="create_<?= $m->id ?>" class="check-item create" <?= $e && $e->can_create ? 'checked':'' ?>>
          </td>
          <td class="text-center">
            <input type="checkbox" name="update_<?= $m->id ?>" class="check-item update" <?= $e && $e->can_update ? 'checked':'' ?>>
          </td>
          <td class="text-center">
            <input type="checkbox" name="delete_<?= $m->id ?>" class="check-item delete" <?= $e && $e->can_delete ? 'checked':'' ?>>
          </td>
          <td class="text-center">
            <!-- Check all per baris/module -->
            <input type="checkbox" class="check-all-row">
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button class="btn btn-primary">Save</button>
    <a href="<?= site_url('rolePermission')?>" class="btn btn-secondary">Back</a>
  </form>
</div>

<script>
  // === CHECK ALL PER MODULE (per baris) ===
  $(document).on("change", ".check-all-row", function(){
    let row = $(this).closest("tr");
    row.find(".check-item").prop("checked", $(this).is(":checked"));
  });

  // Jika semua CRUD dicentang manual, otomatis aktifkan check-all-row
  $(document).on("change", ".check-item", function(){
    let row = $(this).closest("tr");
    let allChecked = row.find(".check-item").length === row.find(".check-item:checked").length;
    row.find(".check-all-row").prop("checked", allChecked);
  });

  // === CHECK ALL GLOBAL (semua module + CRUD) ===
  $("#check-all-global").on("change", function(){
    let checked = $(this).is(":checked");
    $(".check-item, .check-all-row").prop("checked", checked);
  });

  // Jika semua checkbox dicentang manual, otomatis aktifkan check-all-global
  $(document).on("change", ".check-item, .check-all-row", function(){
    let total = $(".check-item, .check-all-row").length;
    let totalChecked = $(".check-item:checked, .check-all-row:checked").length;
    $("#check-all-global").prop("checked", total === totalChecked);
  });
</script>


<script>
$(function(){
  $("#checkAllCreate").on("change", function(){
    $(".create").prop("checked", this.checked);
  });
  $("#checkAllRead").on("change", function(){
    $(".read").prop("checked", this.checked);
  });
  $("#checkAllUpdate").on("change", function(){
    $(".update").prop("checked", this.checked);
  });
  $("#checkAllDelete").on("change", function(){
    $(".delete").prop("checked", this.checked);
  });
});
</script>
