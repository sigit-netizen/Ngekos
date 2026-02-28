<div id="preloader-overlay">
  <div class="loader-container">
    <div class="glowing-ring"></div>
    <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="Logo" class="logo-loader">
  </div>
</div>

<style>
  #preloader-overlay {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    background: rgba(8, 10, 15, 0.4);
    backdrop-filter: blur(20px) saturate(160%);
    -webkit-backdrop-filter: blur(20px) saturate(160%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    opacity: 0;
    animation: preloader-fade-in 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    transition: opacity 0.8s cubic-bezier(0.4, 0, 0.1, 1), visibility 0.8s ease;
  }

  @keyframes preloader-fade-in {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .loader-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 240px;
    height: 240px;
    perspective: 1000px;
  }

  .glowing-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid rgba(54, 178, 178, 0.05);
    border-top: 3px solid #36B2B2;
    border-right: 1px solid rgba(54, 178, 178, 0.2);
    filter: drop-shadow(0 0 15px rgba(54, 178, 178, 0.5));
    animation: ring-rotate 1.8s cubic-bezier(0.5, 0.1, 0.4, 0.9) infinite;
  }

  .glowing-ring::after {
    content: '';
    position: absolute;
    inset: 10px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.03);
    border-bottom: 2px solid rgba(54, 178, 178, 0.4);
    animation: ring-rotate 3s linear infinite reverse;
  }

  .logo-loader {
    width: 150px;
    height: auto;
    z-index: 10;
    filter: drop-shadow(0 0 20px rgba(54, 178, 178, 0.25));
    animation: logo-pulse 2.5s ease-in-out infinite;
  }

  @keyframes logo-pulse {

    0%,
    100% {
      transform: scale(1) translateZ(0);
      filter: drop-shadow(0 0 10px rgba(54, 178, 178, 0.1));
    }

    50% {
      transform: scale(1.08) translateZ(50px);
      filter: drop-shadow(0 0 25px rgba(54, 178, 178, 0.45));
    }
  }

  @keyframes ring-rotate {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .preloader-hidden {
    opacity: 0 !important;
    visibility: hidden;
    pointer-events: none;
    transform: scale(1.1);
  }

  /* Responsive Adjustments */
  @media (max-width: 640px) {
    .loader-container {
      width: 180px;
      height: 180px;
    }

    .logo-loader {
      width: 110px;
    }

    #preloader-overlay {
      backdrop-filter: blur(12px) saturate(140%);
      -webkit-backdrop-filter: blur(12px) saturate(140%);
    }
  }
</style>

<script>
  (function () {
    const preloader = document.getElementById('preloader-overlay');
    if (!preloader) return;

    window.addEventListener('load', function () {
      setTimeout(() => {
        preloader.classList.add('preloader-hidden');
        setTimeout(() => {
          preloader.style.display = 'none';
        }, 800);
      }, 1200); 
    });
  })();
</script>