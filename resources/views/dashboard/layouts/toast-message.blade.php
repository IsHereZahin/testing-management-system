<div class="position-fixed bottom-1 end-1 z-index-2">
    <!-- Success Toast -->
    @if (session('success'))
    <div class="toast fade show p-2 bg-white" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
        <div class="toast-header border-0">
            <i class="material-symbols-rounded text-success me-2">check</i>
            <span class="me-auto font-weight-bold">Testing Management System</span>
            <small class="text-body">Just now</small>
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
    @endif

    <!-- Info Toast -->
    @if (session('info'))
    <div class="toast fade show p-2 mt-2 bg-gradient-info" role="alert" aria-live="assertive" aria-atomic="true" id="infoToast">
        <div class="toast-header bg-transparent border-0">
            <i class="material-symbols-rounded text-white me-2">notifications</i>
            <span class="me-auto text-white font-weight-bold">Testing Management System</span>
            <small class="text-white">Just now</small>
            <i class="fas fa-times text-md text-white ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
        </div>
        <hr class="horizontal light m-0">
        <div class="toast-body text-white">
            {{ session('info') }}
        </div>
    </div>
    @endif

    <!-- Warning Toast -->
    @if (session('warning'))
    <div class="toast fade show p-2 mt-2 bg-white" role="alert" aria-live="assertive" aria-atomic="true" id="warningToast">
        <div class="toast-header border-0">
            <i class="material-symbols-rounded text-warning me-2">travel_explore</i>
            <span class="me-auto font-weight-bold">Testing Management System</span>
            <small class="text-body">Just now</small>
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body">
            {{ session('warning') }}
        </div>
    </div>
    @endif

    <!-- Danger Toast -->
    @if (session('danger'))
    <div class="toast fade show p-2 mt-2 bg-white" role="alert" aria-live="assertive" aria-atomic="true" id="dangerToast">
        <div class="toast-header border-0">
            <i class="material-symbols-rounded text-danger me-2">campaign</i>
            <span class="me-auto text-gradient text-danger font-weight-bold">Testing Management System</span>
            <small class="text-body">Just now</small>
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body">
            {{ session('danger') }}
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElements = document.querySelectorAll('.toast');
        toastElements.forEach(function (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        });
    });
</script>
