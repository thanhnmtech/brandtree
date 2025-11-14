/* ======================================================================================================================================================================================= */
/* ======================================================================================================================================================================================= */
/* ========================================================================GPT CODE - GENERATED DUMMY JS FILE============================================================================= */
/* ========================================================================GPT CODE - GENERATED DUMMY JS FILE============================================================================= */
/* ========================================================================GPT CODE - GENERATED DUMMY JS FILE============================================================================= */
/* ======================================================================================================================================================================================= */
/* ======================================================================================================================================================================================= */
/**
 * Sign In / Sign Up Page Navigation
 * Handle tab switching between login and signup views
 */
document.addEventListener("DOMContentLoaded", function () {
  // Get all navigation links
  const signinLink = document.querySelector(".signin-link");
  const signupLink = document.querySelector(".signup-link");

  // Get all form sections (if they exist)
  const signinForm = document.querySelector(".signin-form-section");
  const signupForm = document.querySelector(".signup-form-section");

  /**
   * Navigate to Sign In page
   */
  function navigateToSignIn(e) {
    e.preventDefault();

    // Remove active class from all links
    document.querySelectorAll(".auth-links a").forEach((link) => {
      link.classList.remove("active");
    });

    // Add active class to signin link
    signinLink.classList.add("active");

    // Navigate to signin route
    window.location.href = "/login";
  }

  /**
   * Navigate to Sign Up page
   */
  function navigateToSignUp(e) {
    e.preventDefault();

    // Remove active class from all links
    document.querySelectorAll(".auth-links a").forEach((link) => {
      link.classList.remove("active");
    });

    // Add active class to signup link
    signupLink.classList.add("active");

    // Navigate to signup route
    window.location.href = "/signup";
  }

  /**
   * Smooth page transition (optional animation effect)
   */
  function smoothTransition() {
    const container = document.querySelector(".signin-container");
    if (container) {
      container.style.transition = "opacity 0.3s ease-in-out";
      container.style.opacity = "0";

      setTimeout(() => {
        container.style.opacity = "1";
      }, 300);
    }
  }

  // Attach event listeners to navigation links
  if (signinLink) {
    signinLink.addEventListener("click", function (e) {
      navigateToSignIn(e);
      smoothTransition();
    });
  }

  if (signupLink) {
    signupLink.addEventListener("click", function (e) {
      navigateToSignUp(e);
      smoothTransition();
    });
  }

  /**
   * Handle all signup link variations
   */
  const allSignupLinks = document.querySelectorAll(
    ".signup-link, .signup-link-inline, .signup-prompt a"
  );
  allSignupLinks.forEach((link) => {
    if (link && !link.classList.contains("signup-link")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        window.location.href = "/signup";
      });
    }
  });

  /**
   * Handle all signin link variations
   */

  const allSigninLinks = document.querySelectorAll(
    ".signin-link, .signin-link-inline, .signin-prompt a"
  );

  allSigninLinks.forEach((link) => {
    if (link && !link.classList.contains("signin-link")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        window.location.href = "/login";
      });
    }
  });

  /**
   * Handle taskbar login/signup buttons
   */
  const btnLogin = document.querySelector(".login-btn");
  const btnSignup = document.querySelector(".signup-btn");

  // Điều hướng khi click
  if (btnLogin) {
    btnLogin.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = "/login";
    });
  }

  if (btnSignup) {
    btnSignup.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = "/signup";
    });
  }

  // Xác định trang hiện tại rồi set active state
  const path = window.location.pathname;

  function setActiveState() {
    if (!btnLogin || !btnSignup) return;

    // Reset
    btnLogin.classList.remove("active");
    btnSignup.classList.remove("active");

    // Gắn active đúng nút
    if (path === "/login" || path === "/") {
      btnLogin.classList.add("active");
    }
    if (path === "/signup") {
      btnSignup.classList.add("active");
    }
  }

  setActiveState();

  /**
   * Optional: Detect current page and set active state
   */
  function setActiveLinkBasedOnCurrentPage() {
    const currentPath = window.location.pathname;

    // Remove active class from all links
    document.querySelectorAll(".auth-links a").forEach((link) => {
      link.classList.remove("active");
    });

    // Add active class based on current page
    if (currentPath === "/login" || currentPath === "/") {
      if (signinLink) signinLink.classList.add("active");
    } else if (currentPath === "/signup") {
      if (signupLink) signupLink.classList.add("active");
    }
  }

  // Set active link on page load
  setActiveLinkBasedOnCurrentPage();

  /**
   * Optional: Show/hide forms based on current page
   */
  function toggleFormVisibility() {
    const currentPath = window.location.pathname;

    if (signinForm && signupForm) {
      if (currentPath === "/login" || currentPath === "/") {
        signinForm.style.display = "flex";
        signupForm.style.display = "none";
      } else if (currentPath === "/signup") {
        signinForm.style.display = "none";
        signupForm.style.display = "flex";
      }
    }
  }

  // Toggle forms on page load
  toggleFormVisibility();

  /**
   * AUTO SCALE Right Marketing Content
   * Ensures the marketing block always fits inside viewport height
   */

  function autoScaleContent() {
    const container = document.querySelector(".signin-right");
    const content = document.querySelector(".marketing-content");
    if (!container || !content) return;

    const viewportHeight = window.innerHeight;
    const contentHeight = content.scrollHeight;

    // 80px is safe padding (header spacing)
    if (contentHeight > viewportHeight - 80) {
      let scaleValue = (viewportHeight - 80) / contentHeight;

      // Prevent scaling below 0.6 (optional)
      if (scaleValue < 0.6) scaleValue = 0.6;

      content.style.transform = `scale(${scaleValue})`;
      content.style.transformOrigin = "top center";
    } else {
      content.style.transform = "scale(1)";
    }
  }

  // Trigger autoscale on load and resize
  autoScaleContent();
  window.addEventListener("resize", autoScaleContent);
});

/**
 * Alternative: Direct navigation functions you can call from HTML
 */
window.goToSignIn = function () {
  window.location.href = "/login";
};

window.goToSignUp = function () {
  window.location.href = "/signup";
};
