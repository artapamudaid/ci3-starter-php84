<div class="card shadow p-4">
  <div class="d-flex justify-content-between mb-3">
    <h5>Roles</h5>
    <a href="<?= site_url('roles/create') ?>" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Role</a>
    <?php if($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
  </div>
    <table class="table">
      <thead><tr><th>#</th><th>Nama Role</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach($roles as $r): ?>
          <tr>
            <td><?= $r->id ?></td>
            <td><?= html_escape($r->name) ?></td>
            <td><?= html_escape($r->description) ?></td>
            <td>
              <a href="<?= site_url('rolePermission/assign/'.$r->id) ?>" class="btn btn-sm btn-warning">Tetapkan Akses Modul</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
</div>
