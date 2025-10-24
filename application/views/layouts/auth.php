


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Inventory App</title>
      
      <!-- Favicon -->
      <link rel="shortcut icon" href="<?= base_url() . 'assets/poshdash/' ?>images/favicon.ico" />
      <link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/' ?>css/backend-plugin.min.css">
      <link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/' ?>css/backend.css?v=1.0.0">
      <link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/' ?>vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/' ?>vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="<?= base_url() . 'assets/poshdash/' ?>vendor/remixicon/fonts/remixicon.css">  

		<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	</head>
  <body class=" ">
    
      <div class="wrapper">
      <section class="login-content">
         <div class="container">
			<?= isset($content) ? $content : '' ?>
         </div>
      </section>
      </div>
    
    <!-- Backend Bundle JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/' ?>js/backend-bundle.min.js"></script>
    
    <!-- Table Treeview JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/' ?>js/table-treeview.js"></script>
    
    <!-- Chart Custom JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/' ?>js/customizer.js"></script>
    
    <!-- Chart Custom JavaScript -->
    <script async src="<?= base_url() . 'assets/poshdash/' ?>js/chart-custom.js"></script>
    
    <!-- app JavaScript -->
    <script src="<?= base_url() . 'assets/poshdash/' ?>js/app.js"></script>


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
  </body>
</html>
