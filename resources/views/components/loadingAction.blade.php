{{-- resources/views/components/loadingAction.blade.php --}}
<div id="loading-overlay" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    text-align: center;
    justify-content: center;
    padding-top: 200px;
    font-size: 24px;
    color: #333;
">
    <div id="spinner" style="
        display: none;
        width: 50px;
        height: 50px;
        border: 6px solid #ccc;
        border-top: 6px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
        margin: auto;
    "></div>
    Loading... Please wait.
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function () {
                // Show the loader overlay
                const loader = document.getElementById('loading-overlay');
                const loadSpinner = document.getElementById('spinner');
                if (loader) {
                    loader.style.display = 'block';
                    loadSpinner.style.display = 'block';
                }
            });
        });
    });
</script>
