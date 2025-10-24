<div class="row align-items-center justify-content-center height-self-center">
		<div class="col-lg-8">
			<div class="card auth-card">
					<div class="card-body p-0">
						<div class="d-flex align-items-center auth-content">
								<div class="col-lg-7 align-self-center">
									<div class="p-3">
											<h2 class="mb-2">Inventory App</h2>
											<form id="loginForm">
												<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
														value="<?= $this->security->get_csrf_hash(); ?>">
												<div class="row">
														<div class="col-lg-12">
															<div class="floating-label form-group">
																	<input class="floating-input form-control" name="username" type="text" placeholder=" ">
																	<label>Username</label>
															</div>
															<div class="invalid-feedback"></div>
														</div>
														<div class="col-lg-12">
															<div class="floating-label form-group">
																	<input class="floating-input form-control" name="password" type="password" placeholder=" ">
																	<label>Password</label>
															</div>
														</div>
														<div class="invalid-feedback"></div>
												</div>
												<button class="btn btn-primary">Login</button>
											</form>
									</div>
								</div>
								<div class="col-lg-5 content-right">
									<img src="<?= base_url() . 'assets/poshdash/' ?>images/login/01.png" class="img-fluid image-right" alt="" width="75%">
								</div>
						</div>
					</div>
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
