const body = document.querySelector("body");
const slideNav = document.getElementById("slide-nav");
const hamburger = document.getElementById("hamburger");
const megaburger = document.getElementById("megaburger");
// const topNav = document.getElementById("top-nav");
const meganav = document.querySelector('.meganav-links');
const main = document.querySelector("main");
let slideNavOpen = false;
let openingModal = false;

function getElement(elRef) {
  const firstChar = elRef.substring(0, 1);
  if (firstChar === ".") {
    return document.querySelector(elRef); // Changed to querySelector for consistency
  } else {
    return document.getElementById(elRef);
  }
}

function toggleSlideNav() {
  if (!slideNav || !hamburger) return;
  if (slideNav.style.opacity === "1") {
    hamburger.classList.remove("show-burger");
    slideNav.classList.remove("isShown");
  } else {
    hamburger.classList.toggle("show-burger");
    slideNav.classList.toggle("isShown");
  }
}

function toggleMegaNav() {
  if (!meganav || !megaburger) return;
  if (meganav.style.opacity === "1") {
    megaburger.classList.remove("show-burger");
    meganav.classList.remove('mega-active');
    if (main) main.classList.remove("blur");
  } else {
    megaburger.classList.toggle("show-burger");
    meganav.classList.toggle('mega-active');
    if (main) main.classList.toggle("blur");
  }
}

function openSlideNav() {
  slideNav.style.opacity = 1;
  hamburger.classList.toggle("show-burger");
  slideNav.style.width = "75%";
  slideNav.style.zIndex = 2;
  setTimeout(() => {
    slideNavOpen = true;
  }, 200);
}

function closeSlideNav() {
  slideNav.style.opacity = 0;
  hamburger.classList.remove("show-burger");
  slideNav.style.width = "0";
  slideNav.style.zIndex = "-1";
  slideNavOpen = false;
}

function openModal(modalId) {
  openingModal = true;
  setTimeout(() => {
    openingModal = false;
  }, 100);
  let pageOverlay = document.getElementById("overlay");
  if (!pageOverlay) {
    const modalContainer = document.createElement("div");
    modalContainer.setAttribute("id", "modal-container");
    modalContainer.setAttribute("style", "z-index: 9999;");
    body.append(modalContainer);
    pageOverlay = document.createElement("div");
    pageOverlay.setAttribute("id", "overlay");
    pageOverlay.setAttribute("style", "z-index: 2");
    body.append(pageOverlay);
    const targetModal = getElement(modalId);
    if (!targetModal) return;
    const targetModalContent = targetModal.innerHTML;
    targetModal.remove();
    const newModal = document.createElement("div");
    newModal.setAttribute("class", "modal");
    newModal.setAttribute("id", modalId);
    newModal.style.zIndex = 4;
    newModal.innerHTML = targetModalContent;
    modalContainer.appendChild(newModal);
    
    // Use requestAnimationFrame to ensure the modal is in the DOM before we try to show it
    requestAnimationFrame(() => {
      newModal.style.display = 'block';
      newModal.style.opacity = 1;
      
      // Get the computed style of the modal
      const style = getComputedStyle(newModal);
      
      // Use the custom property if it's set, otherwise fall back to the default
      const marginTop = style.getPropertyValue('--modal-margin-top').trim() || '12vh';
      
      newModal.style.marginTop = marginTop;
    });
    return newModal;
  }
  return null;
}

function closeModal() {
  const modalContainer = document.getElementById("modal-container");
  if (modalContainer) {
    const openModal = modalContainer.firstChild;
    openModal.style.zIndex = "-4";
    openModal.style.opacity = 0;
    openModal.style.marginTop = "12vh";
    openModal.style.display = "none";
    document.body.appendChild(openModal);
    modalContainer.remove();

    const overlay = document.getElementById("overlay");
    if (overlay) {
      overlay.remove();
    }

    // Dispatch a custom event indicating modal closure
    const event = new Event('modalClosed', { bubbles: true, cancelable: true });
    document.dispatchEvent(event);
  }
}

function autoPopulateSlideNav() {
  const slideNavLinks = document.querySelector("#slide-nav ul");
  if (slideNavLinks && slideNavLinks.getAttribute("auto-populate") === "true") {
    const navLinks = document.querySelector("#top-nav");
    if (navLinks) {
      slideNavLinks.innerHTML = navLinks.innerHTML;
    }
  }
}

function handleSlideNavClick(event) {
  if (slideNavOpen && event.target.id !== "open-btn" && !slideNav.contains(event.target)) {
    closeSlideNav();
  }
}

function handleEscapeKey(event) {
  if (event.key === 'Escape') {
    const modalContainer = document.getElementById("modal-container");
    if (modalContainer) {
      closeModal();
    }
  }
}

function handleModalClick(event) {
  if (openingModal === true) {
    return;
  }

  const modalContainer = document.getElementById("modal-container");
  if (modalContainer) {
    const modal = modalContainer.querySelector('.modal');
    if (modal && !modal.contains(event.target)) {
      closeModal();
    }
  }
}

function openCartDrawer() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-drawer-overlay');
  if (!drawer || !overlay) return;
  drawer.classList.add('is-open');
  overlay.classList.add('is-visible');
  document.body.classList.add('drawer-open');
  loadCartPanel();
}

function closeCartDrawer() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-drawer-overlay');
  if (!drawer || !overlay) return;
  drawer.classList.remove('is-open');
  overlay.classList.remove('is-visible');
  document.body.classList.remove('drawer-open');
}

async function loadCartPanel() {
  const inner = document.getElementById('cart-panel-inner');
  if (!inner) return;
  inner.innerHTML = '<div class="drawer-loading"><i class="fa fa-spinner fa-spin"></i></div>';
  const res = await fetch(document.baseURI + 'products/cart_panel');
  inner.innerHTML = await res.text();
  bindCartDrawerEvents();
  syncCartBadge();
}

function bindCartDrawerEvents() {
  document.querySelectorAll('.cart-drawer-qty-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const body = new URLSearchParams({ product_id: btn.dataset.productId, action: btn.dataset.cartAction });
      await fetch(document.baseURI + 'products/update_cart', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      });
      loadCartPanel();
    });
  });

  document.querySelectorAll('[data-cart-remove]').forEach(btn => {
    btn.addEventListener('click', async () => {
      await fetch(document.baseURI + 'products/remove_from_cart_ajax/' + btn.dataset.cartRemove);
      loadCartPanel();
    });
  });
}

function syncCartBadge() {
  const countEl = document.getElementById('drawer-cart-count');
  const count = countEl ? parseInt(countEl.dataset.count, 10) : 0;
  const icon = document.querySelector('.fa-shopping-basket');
  if (!icon) return;
  let badge = icon.querySelector('.cart-count');
  if (count > 0) {
    if (!badge) {
      badge = document.createElement('span');
      badge.className = 'cart-count';
      icon.appendChild(badge);
    }
    badge.textContent = count;
  } else if (badge) {
    badge.remove();
  }
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeCartDrawer();
});

// Initialize
autoPopulateSlideNav();

// Add event listeners2
body.addEventListener("click", (event) => {
  handleSlideNavClick(event);
  handleModalClick(event);
});

document.addEventListener('keydown', handleEscapeKey);