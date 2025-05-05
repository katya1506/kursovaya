document.addEventListener("DOMContentLoaded", function() {
    // Инициализация IQ теста
    initIQTest();
    
    // Общие функции
    function showAlert(message, type = 'success') {
        const alertBox = document.createElement('div');
        alertBox.className = `alert ${type}`;
        alertBox.textContent = message;
        document.body.appendChild(alertBox);
        
        setTimeout(() => {
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        }, 3000);
    }

    // Модальные окна
    const loginModal = document.getElementById("loginModal");
    const testimonialModal = document.getElementById("testimonialModal");
    const loginBtn = document.getElementById("myBtn");
    const addReviewBtn = document.getElementById("addReviewBtn");
    const closeButtons = document.querySelectorAll('.close');

    function openModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Обработчики открытия модальных окон
    if (loginBtn && loginModal) {
        loginBtn.addEventListener('click', function() {
            openModal(loginModal);
        });
    }

    if (addReviewBtn && testimonialModal) {
        addReviewBtn.addEventListener('click', function() {
            openModal(testimonialModal);
        });
    }

    // Обработчики закрытия модальных окон
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });

    // Закрытие при клике вне модального окна
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target);
        }
    });

    // Инициализация рейтинга
    const stars = document.querySelectorAll('.rating-stars label');
    if (stars) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('for').replace('star', '');
                highlightStars(rating);
            });
        });
    }

    function highlightStars(count) {
        stars.forEach((star, index) => {
            if (star) { // Добавлена проверка
                star.style.color = (5 - index) <= count ? '#FFD700' : '#ddd';
            }
        });
    }

    // Анимация Header'а и логотипа
    const header = document.querySelector('.main-header');
if (header) {
    let lastScroll = 0;
    const headerHeight = header.offsetHeight;
    
    setTimeout(() => {
        header.classList.add('visible');
    }, 300);

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Логика для скрытия/показа хедера
        if (currentScroll > lastScroll && currentScroll > headerHeight) {
            // Скролл вниз
            header.classList.remove('visible');
            header.classList.add('hidden');
        } else if (currentScroll < lastScroll) {
            // Скролл вверх
            header.classList.remove('hidden');
            header.classList.add('visible');
        }
        
        // Ваша существующая логика для стилей при скролле
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
            header.classList.remove('scrolled-down');
        }
        
        if (currentScroll > 200) {
            header.classList.add('scrolled-down');
        } else if (currentScroll < 150) {
            header.classList.remove('scrolled-down');
        }
        
        lastScroll = currentScroll;
    });
}
    // const header = document.querySelector('.main-header');
    // if (header) {
    //     setTimeout(() => {
    //         header.classList.add('visible');
    //     }, 300);

    //     window.addEventListener('scroll', () => {
    //         const currentScroll = window.pageYOffset;
            
    //         if (currentScroll > 50) {
    //             header.classList.add('scrolled');
    //         } else {
    //             header.classList.remove('scrolled');
    //             header.classList.remove('scrolled-down');
    //         }
            
    //         if (currentScroll > 200) {
    //             header.classList.add('scrolled-down');
    //         } else if (currentScroll < 150) {
    //             header.classList.remove('scrolled-down');
    //         }
    //     });
    // }
    
    // Мобильное меню
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.innerHTML = navLinks.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }
    
    // Слайдер
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let slideInterval;
    
    if (slides.length > 0 && dots.length > 0) {
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        
        // Автопереключение слайдов
        slideInterval = setInterval(nextSlide, 5000);
        
        // Клик по точкам
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                clearInterval(slideInterval);
                const slideIndex = parseInt(this.getAttribute('data-slide'));
                showSlide(slideIndex);
                slideInterval = setInterval(nextSlide, 5000);
            });
        });
        
        showSlide(0);
    }
    
    // Карточки услуг
    const cards = document.querySelectorAll('.flip-card');
    if (cards) {
        cards.forEach(card => {
            card.addEventListener('click', function() {
                this.classList.toggle('flipped');
                
                const video = this.querySelector('video');
                if (video) {
                    if (this.classList.contains('flipped')) {
                        video.play().catch(e => console.log('Автовоспроизведение запрещено:', e));
                    } else {
                        video.pause();
                        video.currentTime = 0;
                    }
                }
            });
        });
    }
    
    // Плавная прокрутка
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#' || href === '#!') {
                e.preventDefault();
                return;
            }

            try {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            } catch (error) {
                console.error('Invalid selector:', href);
            }
        });
    });
    
    // Обработка формы заявки
    const repairForm = document.getElementById('repairForm');
    if (repairForm) {
        repairForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message);
                    this.reset();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showAlert('Произошла ошибка при отправке формы', 'error');
            });
        });
    }
});
function initIQTest() {
    const startBtn = document.getElementById('startTest');
    const testContainer = document.getElementById('testContainer');
    const nextBtn = document.getElementById('nextQuestion');
    const submitBtn = document.getElementById('submitTest');
    const testResult = document.getElementById('testResult');
    const discountValue = document.getElementById('discountValue');
    const resultTitle = document.getElementById('resultTitle');
    const resultDescription = document.getElementById('resultDescription');
    const resultCode = document.getElementById('resultCode');
    const currentQuestionEl = document.getElementById('currentQuestion');
    
    const questions = document.querySelectorAll('.iq-question');
    const steps = document.querySelectorAll('.step');
    let currentQuestion = 0;
    
    // Инициализация
    testContainer.style.display = 'none';
    testResult.style.display = 'none';
    
    // Старт теста
    startBtn.addEventListener('click', function() {
        testContainer.style.display = 'block';
        startBtn.style.display = 'none';
        showQuestion(currentQuestion);
    });
    
    // Показать вопрос
    function showQuestion(index) {
        questions.forEach((q, i) => {
            q.classList.toggle('active', i === index);
        });
        
        steps.forEach((step, i) => {
            step.classList.toggle('active', i <= index);
        });
        
        currentQuestionEl.textContent = index + 1;
        
        // Проверяем выбран ли ответ для текущего вопроса
        const selectedOption = questions[index].querySelector('.iq-option.selected');
        nextBtn.disabled = !selectedOption;
        
        // Показываем нужную кнопку
        if (index === questions.length - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'flex';
        } else {
            nextBtn.style.display = 'flex';
            submitBtn.style.display = 'none';
        }
    }
    
    // Выбор ответа
    document.querySelectorAll('.iq-option').forEach(option => {
        option.addEventListener('click', function() {
            const question = this.closest('.iq-question');
            question.querySelectorAll('.iq-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            nextBtn.disabled = false;
        });
    });
    
    // Следующий вопрос
    nextBtn.addEventListener('click', function() {
        if (currentQuestion < questions.length - 1) {
            currentQuestion++;
            showQuestion(currentQuestion);
            nextBtn.disabled = true;
        }
    });
    
    // Отправка результатов
    submitBtn.addEventListener('click', function() {
        let correctAnswers = 0;
        
        questions.forEach(question => {
            const selectedOption = question.querySelector('.iq-option.selected');
            if (selectedOption && selectedOption.dataset.value === question.dataset.correct) {
                correctAnswers++;
            }
        });
        
        // Генерация случайного кода
        const randomCode = Math.floor(1000 + Math.random() * 9000);
        
        // Определение результата
        let discount = 0;
        let title = '';
        
        switch(correctAnswers) {
            case 5: discount = 30; title = 'Гений компьютеров!'; break;
            case 4: discount = 20; title = 'Профессионал'; break;
            case 3: discount = 15; title = 'Опытный пользователь'; break;
            case 2: discount = 10; title = 'Любитель'; break;
            case 1: discount = 5; title = 'Новичок'; break;
            default: discount = 0; title = 'Попробуйте еще раз';
        }
        
        // Отображение результатов
        discountValue.textContent = `${discount}%`;
        resultTitle.textContent = title;
        resultDescription.textContent = `Вы ответили правильно на ${correctAnswers} из ${questions.length} вопросов`;
        resultCode.textContent = randomCode;
        
        testResult.style.display = 'block';
        testContainer.style.display = 'none';
        testResult.scrollIntoView({ behavior: 'smooth' });
    });
}

// Инициализация теста при загрузке
document.addEventListener('DOMContentLoaded', initIQTest);
// function initIQTest() {
//     const startBtn = document.getElementById('startTest');
//     const testContainer = document.getElementById('testContainer');
//     const submitBtn = document.getElementById('submitTest');
//     const testResult = document.getElementById('testResult');
//     const discountValue = document.getElementById('discountValue');
//     const resultDescription = document.getElementById('resultDescription');

//     if (!startBtn || !testContainer || !submitBtn || !testResult) return;

//     testContainer.style.display = 'none';
//     testResult.style.display = 'none';

//     startBtn.addEventListener('click', function() {
//         testContainer.style.display = 'block';
//         startBtn.style.display = 'none';
//     });

//     const options = document.querySelectorAll('.iq-option');
//     options.forEach(option => {
//         option.addEventListener('click', function() {
//             const question = this.closest('.iq-question');
//             question.querySelectorAll('.iq-option').forEach(opt => {
//                 opt.classList.remove('selected');
//             });
//             this.classList.add('selected');
//         });
//     });

//     submitBtn.addEventListener('click', function() {
//         let correctAnswers = 0;
//         const questions = document.querySelectorAll('.iq-question');

//         questions.forEach(question => {
//             const selectedOption = question.querySelector('.iq-option.selected');
//             if (selectedOption && selectedOption.dataset.value === question.dataset.correct) {
//                 correctAnswers++;
//             }
//         });

//         let discount = 0;
//         let description = '';

//         switch(correctAnswers) {
//             case 1: discount = 5; description = 'Новичок'; break;
//             case 2: discount = 10; description = 'Любитель'; break;
//             case 3: discount = 15; description = 'Опытный пользователь'; break;
//             case 4: discount = 20; description = 'Профи'; break;
//             case 5: discount = 30; description = 'Гений компьютерных технологий!'; break;
//             default: discount = 0; description = 'Попробуйте ещё раз!';
//         }

//         discountValue.textContent = discount + '%';
//         resultDescription.textContent = `Вы ответили правильно на ${correctAnswers} из 5 вопросов. Ваш уровень: ${description}`;
//         testResult.style.display = 'block';
//         testContainer.style.display = 'none';
//         testResult.scrollIntoView({ behavior: 'smooth' });
//     });
// }