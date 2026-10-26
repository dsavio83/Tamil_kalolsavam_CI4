/**
* Template Name: NiceAdmin
* Updated: Sep 18 2024 with Bootstrap v5.3.2
* Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener))
    } else {
      select(el, all).addEventListener(type, listener)
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Classic Sidebar Toggle & Active Navigation Handling
   */
  const initSidebar = () => {
    // Toggle sidebar on header hamburger click
    on('click', '.toggle-sidebar-btn', function(e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      select('body').classList.toggle('toggle-sidebar');
      if (window.innerWidth < 1200) {
        document.documentElement.classList.toggle('toggle-sidebar', select('body').classList.contains('toggle-sidebar'));
      }
    });

    // Close mobile sidebar on close button click (delegated capture for mobile reliability)
    document.addEventListener('click', function(e) {
      if (e.target && e.target.closest('.sidebar-close-btn, .sidebar-mobile-toggle-btn, .sidebar-mobile-close-icon')) {
        if (e) {
          e.preventDefault();
          e.stopPropagation();
        }
        select('body').classList.remove('toggle-sidebar');
        document.documentElement.classList.remove('toggle-sidebar');
      }
    }, true);

    // Close mobile sidebar on backdrop click
    on('click', '#sidebarBackdrop', function() {
      select('body').classList.remove('toggle-sidebar');
      document.documentElement.classList.remove('toggle-sidebar');
    });

    // Close mobile sidebar on link click
    on('click', '#sidebar-nav a', function(e) {
      if (window.innerWidth < 1200) {
        const link = e.target.closest('a');
        if (link && !link.hasAttribute('data-bs-toggle') && link.getAttribute('href') !== '#') {
          select('body').classList.remove('toggle-sidebar');
          document.documentElement.classList.remove('toggle-sidebar');
        }
      }
    }, true);

    // Prevent background page scrolling while mobile sidebar is open
    document.addEventListener('touchmove', function(e) {
      if (document.body.classList.contains('toggle-sidebar') && window.innerWidth < 1200) {
        if (!e.target.closest('#sidebar')) {
          e.preventDefault();
        }
      }
    }, { passive: false });

    // Auto-detect and highlight active page in sidebar
    initActiveSidebarNav();
  };

  /**
   * Auto-highlight active navigation item & expand parent dropdown
   */
  const initActiveSidebarNav = () => {
    try {
      const currentUrl = window.location.href.split('#')[0].split('?')[0].replace(/\/+$/, '').toLowerCase();
      const currentPath = window.location.pathname.toLowerCase().replace(/\/+$/, '');
      const navLinks = document.querySelectorAll('#sidebar-nav a');
      let bestMatch = null;
      let maxMatchLen = 0;

      // Clean up previous active states and ensure non-accordion links stay collapsed
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (!link.hasAttribute('data-bs-toggle')) {
          link.classList.add('collapsed');
        }
      });

      // 1. Explicit Domain Mapping for Certificate Sub-routes (Only when on a certificate page)
      const pathStr = (currentPath + ' ' + currentUrl).toLowerCase();
      if (pathStr.includes('certificate')) {
        if (pathStr.includes('school_wise') || pathStr.includes('schoolwise')) {
          bestMatch = document.getElementById('menu-cert-school') || document.querySelector('a[href*="list_school_wise"]');
        } else if (pathStr.includes('item_wise') || pathStr.includes('itemwise') || pathStr.includes('item_certificate')) {
          bestMatch = document.getElementById('menu-cert-item') || document.querySelector('a[href*="list_item_wise"]');
        } else if (pathStr.includes('reg_no_wise') || pathStr.includes('participant_wise') || pathStr.includes('regnowise')) {
          bestMatch = document.getElementById('menu-cert-reg') || document.querySelector('a[href*="list_reg_no_wise"]');
        } else if (pathStr.includes('save_certificate_template') || pathStr.includes('test_certificate') || pathStr.endsWith('/certificate/certificate') || pathStr.endsWith('/certificate/certificate/index') || pathStr.endsWith('/district/certificate')) {
          bestMatch = document.getElementById('menu-cert-template') || document.querySelector('a[href*="certificate/certificate"]') || document.querySelector('a[href*="district/certificate"]');
        }
      }

      // 2. Generic URL Prefix Matching if no explicit match above
      if (!bestMatch) {
        navLinks.forEach(link => {
          const href = link.getAttribute('href');
          if (!href || href === '#' || href.startsWith('javascript:')) return;
          
          try {
            const linkUrl = new URL(href, window.location.origin);
            const linkCleanUrl = linkUrl.href.split('#')[0].split('?')[0].replace(/\/+$/, '').toLowerCase();
            const linkPath = linkUrl.pathname.toLowerCase().replace(/\/+$/, '');

            // Skip matching the general template link against other certificate actions
            if ((linkCleanUrl.endsWith('/certificate/certificate') || linkCleanUrl.endsWith('/certificate/certificate/index')) && 
                (currentPath.includes('item') || currentPath.includes('school') || currentPath.includes('reg_no'))) {
              return;
            }
            
            if (linkCleanUrl && (currentUrl === linkCleanUrl || currentUrl.startsWith(linkCleanUrl + '/'))) {
              if (linkCleanUrl.length > maxMatchLen) {
                maxMatchLen = linkCleanUrl.length;
                bestMatch = link;
              }
            } else if (linkPath && (currentPath === linkPath || currentPath.startsWith(linkPath + '/'))) {
              if (linkPath.length > maxMatchLen) {
                maxMatchLen = linkPath.length;
                bestMatch = link;
              }
            }
          } catch (err) {}
        });
      }

      if (bestMatch) {
        bestMatch.classList.add('active');
        bestMatch.classList.remove('collapsed');
        
        // If it is inside a submenu accordion
        const parentContent = bestMatch.closest('.nav-content');
        if (parentContent) {
          parentContent.classList.add('show');
          const toggleLink = document.querySelector(`[data-bs-target="#${parentContent.id}"]`);
          if (toggleLink) {
            toggleLink.classList.remove('collapsed');
            toggleLink.classList.add('active');
            toggleLink.setAttribute('aria-expanded', 'true');
          }
        }
      }
    } catch (e) {}
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
  } else {
    initSidebar();
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Initiate tooltips
   */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  /**
   * Initiate quill editors
   */
  if (select('.quill-editor-default')) {
    new Quill('.quill-editor-default', {
      theme: 'snow'
    });
  }

  if (select('.quill-editor-bubble')) {
    new Quill('.quill-editor-bubble', {
      theme: 'bubble'
    });
  }

  if (select('.quill-editor-full')) {
    new Quill(".quill-editor-full", {
      modules: {
        toolbar: [
          [{
            font: []
          }, {
            size: []
          }],
          ["bold", "italic", "underline", "strike"],
          [{
              color: []
            },
            {
              background: []
            }
          ],
          [{
              script: "super"
            },
            {
              script: "sub"
            }
          ],
          [{
              list: "ordered"
            },
            {
              list: "bullet"
            },
            {
              indent: "-1"
            },
            {
              indent: "+1"
            }
          ],
          ["direction", {
            align: []
          }],
          ["link", "image", "video"],
          ["clean"]
        ]
      },
      theme: "snow"
    });
  }

  /**
   * Initiate TinyMCE Editor
   */
  if (typeof tinymce !== 'undefined') {
    const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

    tinymce.init({
    selector: 'textarea.tinymce-editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    editimage_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    toolbar_sticky: true,
    toolbar_sticky_offset: isSmallScreen ? 102 : 108,
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: '{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    image_advtab: true,
    link_list: [{
        title: 'My page 1',
        value: 'https://www.tiny.cloud'
      },
      {
        title: 'My page 2',
        value: 'http://www.moxiecode.com'
      }
    ],
    image_list: [{
        title: 'My page 1',
        value: 'https://www.tiny.cloud'
      },
      {
        title: 'My page 2',
        value: 'http://www.moxiecode.com'
      }
    ],
    image_class_list: [{
        title: 'None',
        value: ''
      },
      {
        title: 'Some class',
        value: 'class-name'
      }
    ],
    importcss_append: true,
    file_picker_callback: (callback, value, meta) => {
      /* Provide file and text for the link dialog */
      if (meta.filetype === 'file') {
        callback('https://www.google.com/logos/google.jpg', {
          text: 'My text'
        });
      }

      /* Provide image and alt text for the image dialog */
      if (meta.filetype === 'image') {
        callback('https://www.google.com/logos/google.jpg', {
          alt: 'My alt text'
        });
      }

      /* Provide alternative source and posted for the media dialog */
      if (meta.filetype === 'media') {
        callback('movie.mp4', {
          source2: 'alt.ogg',
          poster: 'https://www.google.com/logos/google.jpg'
        });
      }
    },
    templates: [{
        title: 'New Table',
        description: 'creates a new table',
        content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
      },
      {
        title: 'Starting my story',
        description: 'A cure for writers block',
        content: 'Once upon a time...'
      },
      {
        title: 'New list with dates',
        description: 'New List with dates',
        content: '<div class="mceTmpl"><span class="cdate">cdate</span><br><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
      }
    ],
    template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
    template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
    height: 600,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_class: 'mceNonEditable',
    toolbar_mode: 'sliding',
    contextmenu: 'link image table',
    skin: useDarkMode ? 'oxide-dark' : 'oxide',
    content_css: useDarkMode ? 'dark' : 'default',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });
  }

  /**
   * Initiate Bootstrap validation check
   */
  var needsValidation = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(needsValidation)
    .forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })

  /**
   * Initiate Datatables
   */
  const datatables = select('.datatable', true)
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable);
  })

  /**
   * Autoresize echart charts
   */
  const mainContainer = select('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        select('.echart', true).forEach(getEchart => {
          echarts.getInstanceByDom(getEchart).resize();
        })
      }).observe(mainContainer);
    }, 200);
  }

})();