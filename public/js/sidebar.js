
        (function () {
            const wrapper = document.querySelector(".dashboard-wrapper");
            const sidebar = document.querySelector(".sidebar");
            const toggles = document.querySelectorAll(".sidebar-toggle");
            const fixedToggle = document.getElementById("sidebarOpenFixed");

            // Toggle sidebar visibility from any toggle button
            function updateFixedToggleVisibility() {
                if (!fixedToggle) return;
                if (wrapper.classList.contains("sidebar-hidden")) {
                    fixedToggle.style.display = "inline-flex";
                } else {
                    fixedToggle.style.display = "none";
                }
            }

            toggles.forEach((btn) =>
                btn.addEventListener("click", function (e) {
                    e.stopPropagation();
                    wrapper.classList.toggle("sidebar-hidden");
                    updateFixedToggleVisibility();
                }),
            );

            // Fixed open button toggles sidebar when it's hidden
            if (fixedToggle) {
                fixedToggle.addEventListener("click", function (e) {
                    e.stopPropagation();
                    wrapper.classList.remove("sidebar-hidden");
                    updateFixedToggleVisibility();
                });
            }

            // Close sidebar when clicking outside (useful on small screens)
            document.addEventListener("click", function (e) {
                // if click outside sidebar and not on a toggle, hide it
                if (
                    !sidebar.contains(e.target) &&
                    !Array.from(toggles).some((t) => t.contains(e.target)) &&
                    !fixedToggle.contains(e.target)
                ) {
                    wrapper.classList.add("sidebar-hidden");
                    updateFixedToggleVisibility();
                }
            });

            // initialize fixed toggle visibility on load
            updateFixedToggleVisibility();

            // hide sidebar by default on small screens for a cleaner mobile-first UX
            if (window.innerWidth <= 768) {
                wrapper.classList.add("sidebar-hidden");
                updateFixedToggleVisibility();
            }

            // keep sidebar state consistent when resizing the viewport
            window.addEventListener("resize", function () {
                if (window.innerWidth <= 768) {
                    wrapper.classList.add("sidebar-hidden");
                } else {
                    wrapper.classList.remove("sidebar-hidden");
                }
                updateFixedToggleVisibility();
            });

            // close sidebar when pressing Escape (mobile accessibility)
            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape" && window.innerWidth <= 768) {
                    wrapper.classList.add("sidebar-hidden");
                    updateFixedToggleVisibility();
                }
            });

            // Prevent clicks inside sidebar from closing it (allow anchors to navigate)
            sidebar.addEventListener("click", function (e) {
                if (e.target.closest("a")) return; // allow link clicks
                e.stopPropagation();
            });

            // Ensure sidebar links always navigate (fallback for SPA/overlay issues)
            document.querySelectorAll(".sidebar nav a").forEach((a) => {
                a.addEventListener("click", function (e) {
                    const href = this.getAttribute("href");
                    if (!href || href === "#") return; // ignore placeholders
                    e.preventDefault();
                    window.location.href = href;
                });
            });

            // If logo images are not available, show SVG fallback
            (function () {
                const imgs = sidebar.querySelectorAll(".side-logos img");
                const svgFallback = sidebar.querySelector(".side-logo-svg");
                const anyVisible = Array.from(imgs).some(
                    (i) =>
                        i.complete &&
                        i.naturalWidth > 0 &&
                        i.style.display !== "none",
                );
                if (!anyVisible && svgFallback)
                    svgFallback.style.display = "block";
            })();
        })();