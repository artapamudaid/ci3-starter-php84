<div class="row justify-content-center">
  <div class="col-md-4">
    <div class="card shadow p-4">
      <h4 class="mb-3 text-center">Login</h4>
      <form id="loginForm">
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
				value="<?= $this->security->get_csrf_hash(); ?>">

		<div class="mb-3">
			<label class="form-label">Username</label>
			<input type="text" name="username" class="form-control">
			<div class="invalid-feedback"></div>
		</div>
		<div class="mb-3">
			<label class="form-label">Password</label>
			<input type="password" name="password" class="form-control">
			<div class="invalid-feedback"></div>
		</div>
		<button class="btn btn-primary w-100">Login</button>
		</form>

    </div>
  </div>
</div>

<script>
$(function(){
  $("#loginForm").on("submit", function(e){
    e.preventDefault();
    let form = $(this);

    $.post("<?= site_url('auth/ajax_login') ?>", form.serialize(), function(res){
      csrfHash = res.csrfHash; // update token
      resetErrors(form);

      if(res.status === "error"){
        if(res.errors) showErrors(form, res.errors);
        if(res.message) notify("error", res.message);
      } else if(res.status === "success"){
        notify("success","Login berhasil!");
        setTimeout(()=>{ window.location.href = res.redirect; }, 1500);
      }
    },'json');
  });
});
</script>
