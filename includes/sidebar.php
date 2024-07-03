<!-- BEGIN: Side Menu -->
<nav class="side-nav">
    <a href="" class="intro-x flex items-center pl-5 pt-4">    
        <img alt="Midone Tailwind HTML Admin Template" class="w-6" src="<?php echo $url . '/static resources/images/logo.svg'; ?>">
        <span class="hidden xl:block text-white text-lg ml-3"> Mid<span class="font-medium">one</span> </span>
    </a>
    <div class="side-nav__devider my-6"></div>
    <ul>
        <li>
            <a href="<?php echo $url . '/views/index.php'; ?>" class="side-menu side-menu--active">
                <div class="side-menu__icon"> <i data-feather="home"></i> </div>
                <div class="side-menu__title"> Dashboard </div>
            </a>
        </li>
        <li>
            <a href="javascript:;" class="side-menu side-menu--open">
                <div class="side-menu__icon"> <i data-feather="users"></i> </div>
                <div class="side-menu__title">
                    Profesores 
                    <i data-feather="chevron-down" class="side-menu__sub-icon transform rotate-180"></i>
                </div>
            </a>            
            <ul class="side-menu__sub-open" style="display: block;">
                <li>
                    <a href="<?php echo $url . '/views/teachers/teachers-list.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="list"></i> </div>
                        <div class="side-menu__title"> Listar Profesores </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $url . '/views/teachers/add-teacher.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="plus"></i> </div>
                        <div class="side-menu__title"> Añadir Profesor </div>
                    </a>
                </li>
            </ul>
        </li>   
        <li>
            <a href="javascript:;" class="side-menu side-menu--open">
                <div class="side-menu__icon"> <i data-feather="book"></i> </div>
                <div class="side-menu__title">
                    Asignaturas 
                    <i data-feather="chevron-down" class="side-menu__sub-icon transform rotate-180"></i>
                </div>
            </a>            
            <ul class="side-menu__sub-open" style="display: block;">
                <li>
                    <a href="<?php echo $url . '/views/subjects/subject-list.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="list"></i> </div>
                        <div class="side-menu__title"> Listar Asignaturas </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $url . '/views/subjects/add-subject.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="plus"></i> </div>
                        <div class="side-menu__title"> Añadir Asignatura </div>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="side-menu side-menu--open">
                <div class="side-menu__icon"> <i data-feather="map"></i> </div>
                <div class="side-menu__title">
                    Cedes 
                    <i data-feather="chevron-down" class="side-menu__sub-icon transform rotate-180"></i>
                </div>
            </a>            
            <ul class="side-menu__sub-open" style="display: block;">
                <li>
                    <a href="<?php echo $url . '/views/cedes/cede-list.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="list"></i> </div>
                        <div class="side-menu__title"> Listar Cedes </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $url . '/views/cedes/add-cede.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="plus"></i> </div>
                        <div class="side-menu__title"> Añadir Cede </div>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="side-menu side-menu--open">
                <div class="side-menu__icon"> <i data-feather="dollar-sign"></i> </div>
                <div class="side-menu__title">
                    Pagos 
                    <i data-feather="chevron-down" class="side-menu__sub-icon transform rotate-180"></i>
                </div>
            </a>            
            <ul class="side-menu__sub-open" style="display: block;">
                <li>
                    <a href="<?php echo $url . '/views/pagos/pago-list.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="list"></i> </div>
                        <div class="side-menu__title"> Listar Pagos </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $url . '/views/pagos/add-pago.php'; ?>" class="side-menu">
                        <div class="side-menu__icon"> <i data-feather="plus"></i> </div>
                        <div class="side-menu__title"> Añadir Pago </div>
                    </a>
                </li>
            </ul>
        </li>             
    </ul>
</nav>
<!-- END: Side Menu -->
<script src="<?php echo $url . '/static resources/js/app.js'; ?>"></script>
<!-- END: JS -->
