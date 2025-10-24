<div class="card shadow p-4">
  <h5>Petunjuk Singkat</h5>
	<p>Penginputan data kategori bisa dilakukan dengan menyalin data dari file (.xlsx). Format file excel harus sesuai kebutuhan aplikasi. Silahkan download formatnya <a href="<?= site_url('categories/download_template') ?>"><span class="badge badge-success">DISINI</span></a>
	</p>
	<hr>
  <form action="<?= site_url('categories/do_import') ?>" method="post" enctype="multipart/form-data">
	<div class="mb-3">
	  <label>Pilih File Excel (.xlsx)</label>
	  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
	  <input type="file" name="file" accept=".xlsx" required>
	</div>
	<button type="submit" class="btn btn-success">Import</button>
	<a href="<?= site_url('categories') ?>" class="btn btn-secondary">Kembali</a>
  </form>
</div>


