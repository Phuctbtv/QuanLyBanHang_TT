<div class="d-flex flex-column flex-shrink-0 p-3 bg-white shadow-sm" style="width: 280px; min-height: 80vh; border-radius: 15px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4 fw-bold text-primary">CRM Customer</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php?controller=customer&action=index" class="nav-link  <?php if ($check == "customer"){echo "active";}?>">
                <i class="bi bi-people-fill me-2"></i>
                Danh sách khách hàng
                
            </a>
        </li>
        <li>
            <a href="index.php?controller=Products&action=index" class="nav-link <?php if ($check == "product"){echo "active";}?>" >
                <i class="bi bi-person-plus-fill me-2"></i>
               Danh sách sản phẩm
               
            </a>
        </li>
        <li>
            <a href="index.php?controller=Users&action=index" class="nav-link <?php if ($check == "users"){echo "active";}?>" >
                <i class="bi bi-person-plus-fill me-2"></i>
               Danh sách các tài khoản
               
            </a>
        </li>
        <li>
            <a href="index.php?controller=orders&action=index" class="nav-link <?php if ($check == "orders"){echo "active";}?>" >
                <i class="bi bi-person-plus-fill me-2"></i>
               Danh sách các đơn hàng
               
            </a>
        </li>
         <li>
            <a href="index.php?controller=Categories&action=index" class="nav-link <?php if ($check == "categories"){echo "active";}?>" >
                <i class="bi bi-person-plus-fill me-2"></i>
               Danh sách danh mục
               
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
            <strong>Admin Account</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
            <li><a class="dropdown-item" href="#">Cài đặt</a></li>
            <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Đăng xuất</a></li>
        </ul>
    </div>
</div>