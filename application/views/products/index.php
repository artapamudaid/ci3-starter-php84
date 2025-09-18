<div class="card shadow p-4">
  <div class="d-flex justify-content-between mb-3">
    <h5>Data Produk</h5>
     <?php if (has_permission('products','create')): ?>
      <button class="btn btn-primary" id="btnAdd">Tambah Produk</button>
    <?php endif; ?>
  </div>
  <table id="dataTable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="productForm">
        <div class="modal-header">
          <h5 class="modal-title">Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control">
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-3">
            <label>Harga</label>
            <input type="text" name="price" class="form-control" data-format="number">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
$(function(){
  modal = new bootstrap.Modal(document.getElementById('productModal'));

  table = $("#dataTable").DataTable({
    processing:true,
    serverSide:true,
    ajax:{
      url:"<?= site_url('products/ajax_list') ?>",
      type:"POST",
      data:function(d){
        d['<?= $this->security->get_csrf_token_name(); ?>'] = csrfHash;
      },
      dataSrc:function(json){
        csrfHash = json.csrfHash;
        return json.data;
      }
    },
	columnDefs:[
		{
			targets:2, // kolom ke-3 (Harga)
			render:function(data,type,row){
				if(type === 'display' || type === 'filter'){
				// format ke format Rupiah Indonesia
				return "Rp " + parseInt(data).toLocaleString("id-ID");
				}
				return data; // biarkan original untuk sort & export
			}
		}
	]
  });

  $("#btnAdd").click(function(){
    resetErrors($("#productForm"));
    $("#productForm")[0].reset();
    $("#productForm [name=id]").val("");
    modal.show();
  });

  $("#dataTable").on("click",".edit",function(){
    let id = $(this).data("id");
    $.getJSON("<?= site_url('products/ajax_edit/') ?>"+id,function(res){
      csrfHash = res.csrfHash;
      resetErrors($("#productForm"));
      $("#productForm [name=id]").val(res.data.id);
      $("#productForm [name=name]").val(res.data.name);
      $("#productForm [name=price]").val(formatNumber(res.data.price));
      modal.show();
    });
  });

  $("#productForm").submit(function(e){
    e.preventDefault();
    let form = $(this);
    $.post("<?= site_url('products/ajax_save') ?>", form.serialize(), function(res){
      csrfHash = res.csrfHash;
      resetErrors(form);
      if(res.status=="error"){
        showErrors(form, res.errors);
      }else{
        modal.hide();
        notify("success",res.message);
        table.ajax.reload(null,false);
      }
    },'json');
  });

  $("#dataTable").on("click",".delete",function(){
    let id = $(this).data("id");
    Swal.fire({
      title:"Hapus produk?",
      icon:"warning",
      showCancelButton:true,
      confirmButtonText:"Ya, hapus"
    }).then((r)=>{
      if(r.isConfirmed){
        $.post("<?= site_url('products/ajax_delete/') ?>"+id,{[csrfName]:csrfHash},function(res){
          csrfHash = res.csrfHash;
          if(res.status=="success"){
            notify("success",res.message);
            table.ajax.reload(null,false);
          }
        },'json');
      }
    });
  });
});
</script>
