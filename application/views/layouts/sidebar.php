<div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
	<a href="../backend/index.html" class="header-logo">
		<img src="<?= base_url() . 'assets/poshdash/'; ?>images/logo.png" class="img-fluid rounded-normal light-logo" alt="logo">
	</a>
	<div class="iq-menu-bt-sidebar ml-0">
		<i class="las la-bars wrapper-menu"></i>
	</div>
</div>
<?php
// Ambil menu dari SESSION
$menus = $this->session->userdata('user_menus') ?? array();
$current_segment = $this->uri->segment(1);
?>

<div class="data-scrollbar" data-scroll="1">
		<nav class="iq-sidebar-menu">
				<ul id="iq-sidebar-toggle" class="iq-menu">
						
						<?php foreach($menus as $menu): ?>
								<?php 
								$current_segment = $this->uri->segment(1);
								$has_children = isset($menu['children']) && !empty($menu['children']);
								?>
								
								<?php if (!$has_children): ?>
										<!-- MENU LEAF (Tanpa Submenu) -->
										<li class="<?= ($menu['slug'] == $current_segment) ? 'active' : ''; ?>">
												<a href="<?= site_url($menu['url'] ?? '#') ?>" >
														<?php if ($menu['icon']): ?>
															<i class="<?= $menu['icon'] ?>"></i>
														<?php else: ?>
															
														<?php endif; ?>
														<span class="ml-4"><?= $menu['title'] ?></span>
												</a>
										</li>
										
								<?php else: ?>
										<!-- MENU PARENT (Dengan Submenu) -->
										<li class="<?= ($menu['slug'] == $current_segment) ? 'active' : ''; ?>">
												<a href="#submenu-<?= $menu['id'] ?>" class="collapsed <?= ($menu['slug'] == $current_segment) ? '' : '' ?>" 
													data-toggle="collapse" aria-expanded="<?= ($menu['slug'] == $current_segment) ? 'true' : 'false' ?>">
														<?php if ($menu['icon']): ?>
																
															<i class="<?= $menu['icon'] ?>"></i>
														<?php endif; ?>
														<span class="ml-4"><?= $menu['title'] ?></span>
												</a>
												
												<!-- SUBMENU -->
												<ul id="submenu-<?= $menu['id'] ?>" class="iq-submenu collapse <?= ($menu['slug'] == $current_segment) ? 'show' : '' ?>" 
														data-parent="#iq-sidebar-toggle">
														<?php foreach($menu['children'] as $child): ?>
																<?php $child_active = ($current_segment === ($child['url'] ?? '')); ?>
																<li class="<?= $child_active ? 'active' : '' ?>">
																		<a href="<?= site_url($child['url'] ?? '#') ?>">
																				<i class="las la-minus"></i>
																				<span><?= $child['title'] ?></span>
																		</a>
																</li>
														<?php endforeach; ?>
												</ul>
										</li>
								<?php endif; ?>
								
						<?php endforeach; ?>
				</ul>
		</nav>
</div>
