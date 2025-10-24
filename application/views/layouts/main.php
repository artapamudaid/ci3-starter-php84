

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Inventory App</title>
	
	<!-- Favicon -->
	<link rel="shortcut icon" href="<?= base_url() . 'assets/poshdash/'; ?>images/favicon.ico" />
	<link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/'; ?>css/backend-plugin.min.css">
	<link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/'; ?>css/backend.css?v=1.0.0">
	<link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/'; ?>vendor/@fortawesome/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/'; ?>vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
	<link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/'; ?>vendor/remixicon/fonts/remixicon.css">  
	<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

	<div class="wrapper">
      
      <div class="iq-sidebar  sidebar-default ">
					<?= $this->load->view('layouts/sidebar', [], TRUE) ?>
        </div>      
        <div class="iq-top-navbar">
					<div class="iq-navbar-custom">
						<nav class="navbar navbar-expand-lg navbar-light p-0">
							
							<!-- Logo Section -->
							<div class="iq-navbar-logo d-flex align-items-center justify-content-between">
								<i class="ri-menu-line wrapper-menu"></i>
								<a href="../backend/index.html" class="header-logo d-flex align-items-center">
									<img src="<?= base_url('assets/poshdash/images/logo.png') ?>" 
											class="img-fluid rounded-normal" alt="logo" style="height: 40px;">
									<h5 class="logo-title ml-2 mb-0">Inventory App</h5>
								</a>
							</div>

							<!-- Right Side Navbar -->
							<div class="d-flex align-items-center ml-auto">
								<!-- Toggle Button (Mobile) -->
								<button class="navbar-toggler" type="button" data-toggle="collapse"
												data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
												aria-expanded="false" aria-label="Toggle navigation">
									<i class="ri-menu-3-line"></i>
								</button>

								<!-- Collapsible Menu -->
								<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
									<ul class="navbar-nav ml-auto align-items-center">

										<!-- User Dropdown -->
										<li class="nav-item nav-icon dropdown caption-content">
											<a href="#" class="dropdown-toggle d-flex align-items-center" id="dropdownMenuButton4"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<img src="<?= base_url('assets/poshdash/images/user/1.png') ?>" 
														class="img-fluid rounded-circle" alt="user" style="width: 40px; height: 40px;">
											</a>

											<div class="dropdown-menu dropdown-menu-right iq-sub-dropdown"
													aria-labelledby="dropdownMenuButton4">
												<div class="card shadow-none m-0">
													<div class="card-body p-0 text-center">
														
														<!-- Profile Header -->
														<div class="profile-detail text-center">
															<img src="<?= base_url('assets/poshdash/images/user/1.png') ?>" 
																	alt="profile-img" class="rounded-circle img-fluid avatar-70 mb-2">
														</div>

														<!-- User Info -->
														<div class="p-3 border-top">
															<h5 class="mb-1"><?= $this->session->userdata('user')->email; ?></h5>
															<div class="d-flex align-items-center justify-content-center mt-3">
																<a href="../app/user-profile.html" class="btn btn-sm btn-outline-primary mr-2">
																	<i class="ri-user-line mr-1"></i> Profile
																</a>
																<a href="<?= site_url('auth/logout') ?>" class="btn btn-sm btn-outline-danger">
																	<i class="ri-logout-box-line mr-1"></i> Logout
																</a>
															</div>
														</div>

													</div>
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>

						</nav>
					</div>
				</div>
  
      <div class="content-page">
        <?= isset($content) ? $content : '' ?>
      </div>
    </div>

	<footer class="iq-footer">
		<div class="container-fluid">
			<div class="card">
					<div class="card-body">
							<div class="row">
									<div class="col-lg-6">
											
									</div>
									<div class="col-lg-6 text-right">
											<span class="mr-1"><script>document.write(new Date().getFullYear())</script>©</span> <a href="#" class="">Inventory App</a>.
									</div>
							</div>
					</div>
			</div>
		</div>
	</footer>

  <script>
    // === CSRF HANDLING ===
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    $.ajaxSetup({
      beforeSend: function(xhr, settings) {
        if (["POST", "PUT", "DELETE"].includes(settings.type)) {
          if (typeof settings.data === "string") {
            settings.data += (settings.data ? "&" : "") + csrfName + "=" + csrfHash;
          } else {
            settings.data = settings.data || {};
            settings.data[csrfName] = csrfHash;
          }
        }
      },
      complete: function(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.csrfHash) {
          csrfHash = xhr.responseJSON.csrfHash;
          $("input[name='" + csrfName + "']").val(csrfHash);
        }
      },
      error: function(xhr) {
        if (xhr.status === 403) {
          Swal.fire({
            icon: "warning",
            text: "Session expired, silakan login ulang"
          }).then(() => {
            window.location.href = "<?= site_url('auth/login') ?>";
          });
        }
      }
    });

    // === FORM ERROR HANDLING ===
    function resetErrors(form) {
      form.find(".form-control").removeClass("is-invalid");
      form.find(".invalid-feedback").remove();
    }

    function showErrors(form, errors) {
      $.each(errors, function(key, val) {
        let input = form.find("[name=" + key + "]");
        input.addClass("is-invalid");
        if (!input.next(".invalid-feedback").length) {
          input.after('<div class="invalid-feedback"></div>');
        }
        input.next(".invalid-feedback").text(val);
      });
    }

    // === NOTIFY HELPER ===
    function notify(type, message) {
      Swal.fire({
        icon: type,
        text: message,
        showConfirmButton: false,
        timer: 2000
      });
    }
  </script>

  <script src="<?= base_url() . 'assets/js/helpers/number-format.js' ?>"></script>
  <!-- Backend Bundle JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/'; ?>js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/'; ?>js/table-treeview.js"></script>
    
    <!-- Chart Custom JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/'; ?>js/customizer.js"></script>
    
    <!-- Chart Custom JavaScript -->
    <script async src="<?= base_url() . 'assets/poshdash/'; ?>js/chart-custom.js"></script>
    
    <!-- app JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/'; ?>js/app.js"></script>
  	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>
</html>
