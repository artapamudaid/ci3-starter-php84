<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? html_escape($title) : 'CI 3 Stater' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="<?= base_url() . 'assets/dark-mode/style.css' ?>">
</head>
<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="<?= site_url() ?>">CI 3 Stater</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php if($this->session->userdata('user')): ?>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('products') ?>">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('access') ?>">Role</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('auth/logout') ?>">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('auth/login') ?>">Login</a></li>
          <?php endif; ?>
          <li class="nav-item">
            <button id="darkModeToggle" class="btn btn-sm btn-outline-light ms-2">🌙 </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <div class="container py-4">
    <?= isset($content) ? $content : '' ?>
  </div>

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
  <script src="<?= base_url() . 'assets/dark-mode/script.js' ?>"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>
