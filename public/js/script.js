/**
 * Клиентские скрипты для CRM системы
 * Обеспечивает интерактивность и AJAX функциональность
 */

document.addEventListener("DOMContentLoaded", function() {
    console.log("=== DOM Content Loaded ===");
    
    // Инициализация всех компонентов после загрузки DOM
    initSidebar();
    initDashboard();
    // initForms(); // Убираем эту строку, так как функция не определена
    initNavigation();
    
    console.log("=== All components initialized ===");
});

/**
 * Инициализация бокового меню
 */
function initSidebar() {
    console.log('initSidebar called');
    
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const logoIcon = document.querySelector('.logo i'); // Добавляем иконку логотипа
    
    console.log('Sidebar element:', sidebar);
    console.log('Menu toggle element:', menuToggle);
    console.log('Sidebar collapse element:', sidebarCollapse);
    console.log('Logo icon element:', logoIcon);

    // Переключение состояния бокового меню
    function toggleSidebar() {
        console.log('toggleSidebar called');
        sidebar.classList.toggle('collapsed');
        
        // Сохраняем состояние в localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        
        // Обновляем иконку кнопки сворачивания
        if (sidebarCollapse) {
            const icon = sidebarCollapse.querySelector('i');
            if (icon) {
                if (isCollapsed) {
                    icon.className = 'bi bi-chevron-right';
                } else {
                    icon.className = 'bi bi-chevron-left';
                }
            }
        }
        
        console.log('Sidebar collapsed:', isCollapsed);
    }

    // Восстанавливаем состояние из localStorage
    const savedState = localStorage.getItem('sidebarCollapsed');
    console.log('Saved sidebar state:', savedState);
    if (savedState === 'true') {
        sidebar.classList.add('collapsed');
        // Обновляем иконку если sidebar уже свернут
        if (sidebarCollapse) {
            const icon = sidebarCollapse.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-chevron-right';
            }
        }
    }

    // Обработчик для кнопки сворачивания
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function(e) {
            console.log('Sidebar collapse clicked');
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Обработчик для иконки логотипа - разворачивает меню
    if (logoIcon) {
        console.log('Adding click listener to logo icon');
        logoIcon.style.cursor = 'pointer'; // Делаем курсор указателем
        
        logoIcon.addEventListener('click', function(e) {
            console.log('Logo icon clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Если меню свернуто - разворачиваем его
            if (sidebar.classList.contains('collapsed')) {
                toggleSidebar();
            }
            // Если меню развернуто - ничего не делаем (или можно добавить другую логику)
        });
    }

    // Обработчик для мобильного меню
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            console.log('Menu toggle clicked');
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });
    }

    // Закрытие меню на мобильных при клике вне меню
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar && 
            !sidebar.contains(e.target) && 
            menuToggle && 
            !menuToggle.contains(e.target)) {
            sidebar.classList.remove('mobile-open');
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('mobile-open');
        }
    });
    
    console.log('initSidebar completed');
}

/**
 * Инициализация функциональности дашборда
 */
function initDashboard() {
    const cards = document.querySelectorAll(".dashboard-card");
    
    cards.forEach(card => {
        card.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-primary") || 
                e.target.classList.contains("btn-secondary")) {
                // Обработка кликов по кнопкам в карточках
                handleCardAction(this, e.target);
            }
        });
    });
}

/**
 * Обработка действий в карточках дашборда
 */
function handleCardAction(card, button) {
    const cardTitle = card.querySelector("h3").textContent;
    const buttonText = button.textContent;
    
    console.log(`Действие: ${buttonText} для ${cardTitle}`);
    
    // Здесь можно добавить логику для различных действий
    switch(cardTitle) {
        case "Клиенты":
            if (buttonText.includes("Просмотр")) {
                window.location.href = "clients.php";
            } else if (buttonText.includes("Управление")) {
                showModal("Управление клиентами", "<p>Панель управления клиентами находится в разработке.</p>");
            }
            break;
        case "Задачи":
            if (buttonText.includes("Просмотр")) {
                window.location.href = "tasks.php";
            } else if (buttonText.includes("Управление")) {
                showModal("Управление задачами", "<p>Панель управления задачами находится в разработке.</p>");
            }
            break;
        case "Отчеты":
            if (buttonText.includes("Просмотр")) {
                window.location.href = "reports.php";
            } else if (buttonText.includes("Создать")) {
                showModal("Создание отчета", "<p>Форма создания отчета находится в разработке.</p>");
            }
            break;
    }
}

/**
 * Инициализация навигации
 */
function initNavigation() {
    // Базовая инициализация навигации
    console.log('Navigation initialized');
}

/**
 * Показать модальное окно
 */
function showModal(title, content) {
    // Создание модального окна
    const modal = document.createElement("div");
    modal.className = "modal";
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>${title}</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                ${content}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Обработчик закрытия
    const closeBtn = modal.querySelector(".modal-close");
    closeBtn.addEventListener("click", function() {
        closeModal(modal);
    });
    
    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    // Закрытие по ESC
    document.addEventListener("keydown", function closeOnEsc(e) {
        if (e.key === "Escape") {
            closeModal(modal);
            document.removeEventListener("keydown", closeOnEsc);
        }
    });
}

/**
 * Закрыть модальное окно
 */
function closeModal(modal) {
    modal.style.opacity = "0";
    setTimeout(() => {
        if (modal.parentNode) {
            modal.parentNode.removeChild(modal);
        }
    }, 300);
}

/**
 * AJAX запросы
 */
class CRM_API {
    static async request(endpoint, data = {}, method = "POST") {
        try {
            const response = await fetch(endpoint, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: method !== "GET" ? JSON.stringify(data) : null
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error("API Request failed:", error);
            throw error;
        }
    }
}

/**
 * Глобальный объект CRM для доступа из консоли
 */
window.CRM = {
    showModal: showModal,
    API: CRM_API
};

// Утилиты для работы с датами
CRM.utils = {
    formatDate: function(date) {
        return new Date(date).toLocaleDateString("ru-RU");
    },
    
    formatDateTime: function(date) {
        return new Date(date).toLocaleString("ru-RU");
    },
    
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};