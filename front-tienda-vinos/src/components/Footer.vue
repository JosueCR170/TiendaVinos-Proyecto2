<template>
  <footer class="site-footer">
    <div class="footer-inner">

      <!-- Marca -->
      <div class="footer-brand">
        <p class="footer-logo">La Última Botella</p>
        <p class="footer-tagline">
          Curaduría exclusiva de vinos del viejo y nuevo mundo.
          Seleccionamos historias, descorchamos momentos.
        </p>
      </div>

      <!-- Redes sociales -->
      <div class="footer-social">
        <a href="#" title="Instagram" class="social-link">
          <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="#" title="Facebook" class="social-link">
          <i class="fa-brands fa-facebook"></i>
        </a>
        <a href="#" title="Twitter / X" class="social-link">
          <i class="fa-brands fa-x-twitter"></i>
        </a>
      </div>

      <!-- Divisor -->
      <div class="footer-divider"></div>

      <!-- Copyright -->
      <div class="footer-copy">
        © {{ currentYear }} La Última Botella. Todos los derechos reservados.<br>
        <span class="footer-credits">Diseñado y Desarrollado por Daniel y Josué</span>
      </div>

    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style scoped>
.site-footer {
  width: 100%;
  padding: 5rem 3rem;
  background-color: #e4e4cc;
  border-top: 1px solid rgba(218, 193, 191, 0.3);
}

.footer-inner {
  max-width: 56rem;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 2.5rem;
}

/* Marca */
.footer-logo {
  font-family: 'Noto Serif', serif;
  font-size: 1.875rem;
  font-style: italic;
  color: #2a0002;
  margin: 0 0 1rem 0;
}

.footer-tagline {
  font-family: 'Manrope', sans-serif;
  font-size: 0.875rem;
  color: rgba(27, 29, 14, 0.6);
  max-width: 24rem;
  margin: 0;
  line-height: 1.6;
}

/* Social */
.footer-social {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.social-link {
  color: rgba(42, 0, 2, 0.7);
  font-size: 1.25rem;
  text-decoration: none;
  transition: color 0.2s;
}

.social-link:hover { color: #2a0002; }

/* Divisor */
.footer-divider {
  width: 4rem;
  height: 1px;
  background-color: rgba(42, 0, 2, 0.1);
}

/* Copyright */
.footer-copy {
  font-family: 'Manrope', sans-serif;
  font-size: 0.625rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(27, 29, 14, 0.5);
  line-height: 1.8;
}

.footer-credits {
  display: block;
  margin-top: 0.5rem;
  font-style: italic;
  opacity: 0.7;
  color: #78716c;
  text-transform: none;
  letter-spacing: normal;
}
</style>