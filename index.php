<?php
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => 'root',
    'name' => 'my_database'
];
function initializeDatabase($config) {
    $conn = new mysqli($config['host'], $config['user'], $config['pass']);
    
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Создаем базу данных если не существует
    $conn->query("CREATE DATABASE IF NOT EXISTS {$config['name']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($config['name']);

    // Создаем таблицу пользователей
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Создаем таблицу отзывов
    $conn->query("CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        text TEXT NOT NULL,
        avatar VARCHAR(255),
        rating INT NOT NULL,
        date DATETIME NOT NULL
    )");

    $conn->close();
}

initializeDatabase($db_config);
// Обработка отзывов
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_testimonial'])) {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);

    $name = $conn->real_escape_string($_POST['name']);
    $text = $conn->real_escape_string($_POST['text']);
    $avatar = $conn->real_escape_string($_POST['avatar']);
    $rating = (int)$_POST['rating'];
    $date = date('Y-m-d H:i:s');

    if (empty($avatar)) {
        $gender = rand(0, 1) ? 'men' : 'women';
        $avatar = "https://randomuser.me/api/portraits/{$gender}/".rand(1, 99).".jpg";
    }

    $stmt = $conn->prepare("INSERT INTO testimonials (name, text, avatar, rating, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $name, $text, $avatar, $rating, $date);

    if ($stmt->execute()) {
        echo "<script>
                document.getElementById('testimonialModal').style.display = 'none';
                showAlert('Отзыв успешно добавлен!');
                setTimeout(() => location.reload(), 1500);
              </script>";
    } else {
        echo "<script>showAlert('Ошибка: {$stmt->error}', 'error');</script>";
    }

    $stmt->close();
    $conn->close();
}

// Получаем отзывы для отображения
$conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
$testimonials = [];
$result = $conn->query("SELECT * FROM testimonials ORDER BY date DESC");
if ($result) {
    $testimonials = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartService</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap">
    <link rel="stylesheet" href="style.css">
 
</head>
<body>

 <!-- Анимированный Header -->
 <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a style="cursor: no-drop;" href="#" class="logo">
                    <i class="fas fa-tools logo-icon"></i>
                    <span class="logo-text">SmartService</span>
                </a>
                
                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav class="nav-links">
                    <a href="#services">Услуги</a>
                    <a href="#benefits">Преимущества</a>
                    <a href="#testimonials">Отзывы</a>
                    <a href="#contacts">Контакты</a>
                    <button id="myBtn" class="login-btn">Войти</button>
                    <!-- <a href="#login" class="login-btn">Войти</a> -->
                </nav>
            </div>
        </div>
    </header>
   

    <!-- Слайдер -->
    <section class="hero-slider">
        <!-- Слайд 1 -->
        <div class="slide active" style="background-image: url('img/Снимок\ экрана\ 2025-04-07\ 230357.png');">
            <div class="slide-content">
                <h1 class="animate" style="cursor: default;">Профессиональный ремонт компьютеров</h1>
                <p class="hero-text animate delay-1 " style="cursor: default;">Оставьте заявку и получите бесплатную диагностику!</p>
                <a href="#form" style="cursor: pointer;" class="btn animate delay-2">Вызвать мастера</a>
                
            </div>
        </div>
        
        <!-- Слайд 2 -->
        <div class="slide" style="background-image: url('img/in-out-pc-clean-form-inside-shutterstock_2306932401_copy.jpg');">
            <div class="slide-content">
                <h1 id="text-zag" class="animate" style="cursor: default;" >Чистка и обслуживание</h1>
                <p class="hero-text animate delay-1" style="cursor: default;">Продлите жизнь компьютеру профессиональной чисткой</p>
                <a href="#form" class="btn animate delay-2">Заказать чистку</a>
            </div>
        </div>
        
        <!-- Слайд 3 -->
        <div class="slide" style="background-image: url('img/мужик\ стол.png');">
            <div class="slide-content">
                <h1 class="animate" style="cursor: default;">Установка программ и ОС</h1>
                <p class="hero-text animate delay-1" style="cursor: default;">Настроим компьютер для максимальной производительности</p>
                <a href="#form" class="btn animate delay-2">Заказать установку</a>
            </div>
        </div>
        
        <!-- Навигация слайдера -->
        <div class="slider-nav">
            <div class="slider-dot active" data-slide="0"></div>
            <div class="slider-dot" data-slide="1"></div>
            <div class="slider-dot" data-slide="2"></div>
        </div>
    </section>

    <!-- Секция услуг -->
    <section id="services" class="services">
    <div class="container">
        <h2 class="section-title animate" style="cursor: default;">Наши услуги</h2>
        <div class="services-grid">
            <!-- Карточка 1 -->
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front service-card animate delay-1">
                        <div class="service-icon"><i class="fas fa-tools"></i></div>
                        <h3  class="service-title">Замена комплектующих</h3>
                        <p>Профессиональная замена любых компонентов компьютера с гарантией</p>
                    </div>
                    <div class="flip-card-back">
                        <video muted loop playsinline>
                            <source src="img/zamena.mp4" type="video/mp4">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                </div>
            </div>
            
            <!-- Карточка 2 -->
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front service-card animate delay-2">
                        <div class="service-icon"><i class="fas fa-broom"></i></div>
                        <h3 class="service-title">Чистка от пыли</h3>
                        <p>Полная чистка системы охлаждения с заменой термопасты</p>
                    </div>
                    <div class="flip-card-back">
                        <video muted loop playsinline>
                            <source src="img/istockphoto-2190757851-640_adpp_is.mp4" type="video/mp4">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                </div>
            </div>
            
            <!-- Карточка 3 -->
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front service-card animate delay-1">
                        <div class="service-icon"><i class="fas fa-laptop"></i></div>
                        <h3 class="service-title">Установка ОС и программ</h3>
                        <p>Настройка Windows/Linux и необходимого программного обеспечения</p>
                    </div>
                    <div class="flip-card-back">
                        <video muted loop playsinline>
                            <source src="img/установкаОС.mp4" type="video/mp4">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                </div>
            </div>
            
            <!-- Карточка 4 -->
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front service-card animate delay-2">
                        <div class="service-icon"><i class="fas fa-desktop"></i></div>
                        <h3 class="service-title">Диагностика</h3>
                        <p>Бесплатная диагностика неисправностей вашего компьютера</p>
                    </div>
                    <div class="flip-card-back">
                        <video muted loop playsinline>
                            <source src="img/диагностика.mp4" type="video/mp4">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- <section id="services" class="services">
        <div class="container">
            <h2 class="section-title animate">Наши услуги</h2>
            <div class="services-grid">
                <div class="service-card animate delay-1">
                    <div class="service-icon">🔧</div><h3 class="service-title">Замена комплектующих</h3>
                    <p>Профессиональная замена любых компонентов компьютера с гарантией</p>
                </div>
                <div class="service-card animate delay-2">
                    <div class="service-icon">🧹</div>
                    <h3 class="service-title">Чистка от пыли</h3>
                    <p>Полная чистка системы охлаждения с заменой термопасты</p>
                </div>
                <div class="service-card animate delay-1">
                    <div class="service-icon">💻</div>
                    <h3 class="service-title">Установка ОС и программ</h3>
                    <p>Настройка Windows/Linux и необходимого программного обеспечения</p>
                </div>
                <div class="service-card animate delay-2">
                    <div class="service-icon">🖥️</div>
                    <h3 class="service-title">Диагностика</h3>
                    <p>Бесплатная диагностика неисправностей вашего компьютера</p>
                </div>
            </div>
        </div>
    </section> -->

     <!-- IQ тест -->

     <div id="iq-test-wrapper">
     <h2 class="section-title animate" style="cursor: default;">Пройдите IQ-тест</h2>
    <div class="iq-discount-block">
        <div class="iq-discount-header">
            <div class="iq-discount-badge">IQ ТЕСТ</div>
            <h1 class="iq-discount-title">Получите персональную скидку на ремонт</h1>
            <p class="iq-discount-subtitle">Ответьте на 5 вопросов и узнайте свой размер скидки</p>
        </div>
        
        <button class="iq-start-btn pulse" id="startTest">
            <span>Начать тест</span>
            <svg width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg>
        </button>
        
        <div class="iq-test-container" id="testContainer">
            <div class="iq-test-progress">
                <div class="progress-steps">
                    <span class="step active"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
                <div class="progress-text">Вопрос <span id="currentQuestion">1</span> из 5</div>
            </div>
            
            <div class="iq-questions-container">
                <div class="iq-question active" data-question="1" data-correct="2">
                    <div class="iq-question-header">
                        <div class="question-number">01</div>
                        <h2 class="iq-question-text">Сколько бит в 1 байте?</h2>
                    </div>
                    <div class="iq-options">
                        <div class="iq-option" data-value="1">
                            <div class="option-selector"></div>
                            <div class="option-text">4</div>
                        </div>
                        <div class="iq-option" data-value="2">
                            <div class="option-selector"></div>
                            <div class="option-text">8</div>
                        </div>
                        <div class="iq-option" data-value="3">
                            <div class="option-selector"></div>
                            <div class="option-text">16</div>
                        </div>
                    </div>
                </div>
                
                <!-- Остальные вопросы с аналогичной структурой -->
                <div class="iq-question" data-question="2" data-correct="3">
                    <div class="iq-question-header">
                        <div class="question-number">02</div>
                        <h2 class="iq-question-text">Какой компонент отвечает за охлаждение процессора?</h2>
                    </div>
                    <div class="iq-options">
                        <div class="iq-option" data-value="1">
                            <div class="option-selector"></div>
                            <div class="option-text">Видеокарта</div>
                        </div>
                        <div class="iq-option" data-value="2">
                            <div class="option-selector"></div>
                            <div class="option-text">Блок питания</div>
                        </div>
                        <div class="iq-option" data-value="3">
                            <div class="option-selector"></div>
                            <div class="option-text">Кулер</div>
                        </div>
                    </div>
                </div>
                
                <!-- Вопрос 3 -->
                <div class="iq-question" data-question="3" data-correct="2">
                    <div class="iq-question-header">
                        <div class="question-number">03</div>
                        <h2 class="iq-question-text">Если 5 компьютеров чинятся за 5 часов, сколько нужно времени для 10 компьютеров?</h2>
                    </div>
                    <div class="iq-options">
                        <div class="iq-option" data-value="1">
                            <div class="option-selector"></div>
                            <div class="option-text">10 часов</div>
                        </div>
                        <div class="iq-option" data-value="2">
                            <div class="option-selector"></div>
                            <div class="option-text">5 часов</div>
                        </div>
                        <div class="iq-option" data-value="3">
                            <div class="option-selector"></div>
                            <div class="option-text">2,5 часа</div>
                        </div>
                    </div>
                </div>
                
                <!-- Вопрос 4 -->
                <div class="iq-question" data-question="4" data-correct="1">
                    <div class="iq-question-header">
                        <div class="question-number">04</div>
                        <h2 class="iq-question-text">Какая клавиша открывает «Диспетчер задач»?</h2>
                    </div>
                    <div class="iq-options">
                        <div class="iq-option" data-value="1">
                            <div class="option-selector"></div>
                            <div class="option-text">Ctrl + Alt + Del</div>
                        </div>
                        <div class="iq-option" data-value="2">
                            <div class="option-selector"></div>
                            <div class="option-text">Win + R</div>
                        </div>
                        <div class="iq-option" data-value="3">
                            <div class="option-selector"></div>
                            <div class="option-text">Shift + Esc</div>
                        </div>
                    </div>
                </div>
                
                <!-- Вопрос 5 -->
                <div class="iq-question" data-question="5" data-correct="3">
                    <div class="iq-question-header">
                        <div class="question-number">05</div>
                        <h2 class="iq-question-text">Какой формат файла является образом диска?</h2>
                    </div>
                    <div class="iq-options">
                        <div class="iq-option" data-value="1">
                            <div class="option-selector"></div>
                            <div class="option-text">.TXT</div>
                        </div>
                        <div class="iq-option" data-value="2">
                            <div class="option-selector"></div>
                            <div class="option-text">.EXE</div>
                        </div>
                        <div class="iq-option" data-value="3">
                            <div class="option-selector"></div>
                            <div class="option-text">.ISO</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="iq-navigation">
                <button class="iq-next-btn" id="nextQuestion" disabled>
                    <span>Следующий вопрос</span>
                    <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg>
                </button>
                <button class="iq-submit-btn" id="submitTest" style="display: none;">
                    <span>Узнать результат</span>
                    <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M9,20.42L2.79,14.21L5.62,11.37L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/></svg>
                </button>
            </div>
        </div>
        
        <div class="iq-result" id="testResult">
            <div class="result-card">
                <div class="result-badge">Ваш результат</div>
                <div class="discount-circle">
                    <div class="discount-value" id="discountValue">0%</div>
                    <div class="discount-label">скидка</div>
                </div>
                <div class="result-details">
                    <h3 class="result-title" id="resultTitle">Новичок</h3>
                    <p class="result-description" id="resultDescription">Вы ответили правильно на 1 из 5 вопросов</p>
                </div>
                <div class="result-footer">
                    Назовите этот код менеджеру: <span class="result-code">IQ-<span id="resultCode">0000</span></span>
                </div>
            </div>
        </div>
        
        <div class="iq-terms">
            * Скидка действует 3 дня с момента прохождения теста. Не суммируется с другими акциями.
        </div>
    </div>
</div>
     <!-- <div id="iq-test-wrapper">
        <div class="iq-discount-block">
            <div class="iq-discount-header">
                <div class="iq-discount-title">🔧 IQ-скидка на ремонт компьютера!</div>
                <div class="iq-discount-subtitle">Пройдите быстрый тест и получите скидку до 30%!</div>
            </div>
            
            <button class="iq-start-btn" id="startTest">Пройти IQ-тест</button>
            
            <div class="iq-test-container" id="testContainer">
                <div class="iq-question" data-correct="2">
                    <div class="iq-question-text">1️⃣ Сколько бит в 1 байте?</div>
                    <div class="iq-option" data-value="1">4</div>
                    <div class="iq-option" data-value="2">8</div>
                    <div class="iq-option" data-value="3">16</div>
                </div>
                
                <div class="iq-question" data-correct="3">
                    <div class="iq-question-text">2️⃣ Какой компонент отвечает за охлаждение процессора?</div>
                    <div class="iq-option" data-value="1">Видеокарта</div>
                    <div class="iq-option" data-value="2">Блок питания</div>
                    <div class="iq-option" data-value="3">Кулер</div>
                </div>
                
                <div class="iq-question" data-correct="2">
                    <div class="iq-question-text">3️⃣ Если 5 компьютеров чинятся за 5 часов, сколько нужно времени для 10 компьютеров?</div>
                    <div class="iq-option" data-value="1">10 часов</div>
                    <div class="iq-option" data-value="2">5 часов</div>
                    <div class="iq-option" data-value="3">2,5 часа</div>
                </div>
                
                <div class="iq-question" data-correct="1">
                    <div class="iq-question-text">4️⃣ Какая клавиша открывает «Диспетчер задач»?</div>
                    <div class="iq-option" data-value="1">Ctrl + Alt + Del</div>
                    <div class="iq-option" data-value="2">Win + R</div>
                    <div class="iq-option" data-value="3">Shift + Esc</div>
                </div>
                
                <div class="iq-question" data-correct="3">
                    <div class="iq-question-text">5️⃣ Какой формат файла является образом диска?</div>
                    <div class="iq-option" data-value="1">.TXT</div>
                    <div class="iq-option" data-value="2">.EXE</div>
                    <div class="iq-option" data-value="3">.ISO</div>
                </div>
                
                <button class="iq-submit-btn" id="submitTest">Узнать результат</button>
            </div>
            
            <div class="iq-result" id="testResult">
                <div class="iq-result-title">Ваш результат:</div>
                <div class="iq-result-discount" id="discountValue">0%</div>
                <div class="iq-result-desc" id="resultDescription"></div>
                <div class="iq-result-desc">Назовите этот результат менеджеру при заказе, чтобы получить скидку!</div>
            </div>
            
            <div class="iq-terms">
                * Скидка действует 3 дня. Предложение не суммируется с другими акциями.
            </div>
        </div>
    </div> -->
   
    <!-- Секция преимуществ -->
    <section id="benefits" class="benefits">
        <div class="container">
            <h2 class="section-title animate" style="cursor: default;">Почему выбирают нас</h2>
            <div class="benefits-list">
                <div class="benefit-item animate delay-1">
                    <div class="benefit-icon" >🔍</div>
                    <div>
                        <h3 style="cursor: default;">Бесплатная диагностика</h3>
                        <p style="cursor: default;">Точное определение проблемы без обязательного ремонта</p>
                    </div>
                </div>
                <div class="benefit-item animate delay-2">
                    <div class="benefit-icon" style="cursor: default;">⏱️</div>
                    <div>
                        <h3 style="cursor: default;">Выезд в день обращения</h3>
                        <p style="cursor: default;">Мастер приедет к вам в течение 2 часов после заявки</p>
                    </div>
                </div>
                <div class="benefit-item animate delay-1">
                    <div class="benefit-icon">💰</div>
                    <div>
                        <h3 style="cursor: default;">Фиксированные цены</h3>
                        <p style="cursor: default;">Никаких скрытых платежей - стоимость известна заранее</p>
                    </div>
                </div>
                <div class="benefit-item animate delay-2">
                    <div class="benefit-icon">🛡️</div>
                    <div>
                        <h3 style="cursor: default;">Гарантия 1 год</h3>
                        <p style="cursor: default;">Бесплатное устранение проблем по гарантии</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Форма заявки -->
    <section id="form" class="contact-form">
    <div class="container">
        <h2 class="section-title animate" style="cursor: default;">Оставьте заявку</h2>
        <form id="repairForm" method="POST">
            <div class="form-group animate delay-1">
                <label for="name">Ваше имя</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group animate delay-2">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group animate delay-1">
                <label for="problem">Тип проблемы</label>
                <select id="problem" name="problem">
                    <option value="diagnostics">Диагностика</option>
                    <option value="cleaning">Чистка от пыли</option>
                    <option value="components">Замена комплектующих</option>
                    <option value="os">Установка ОС</option>
                    <option value="other">Другое</option>
                </select>
            </div>
            <button id="zayavka" type="submit" class="btn animate delay-2">Отправить заявку</button>
        </form>
    </div>
</section>
    <!-- <section id="form" class="contact-form">
        <div class="container">
            <h2 class="section-title animate">Оставьте заявку</h2>
            <form id="repairForm">
                <div class="form-group animate delay-1">
                    <label for="name">Ваше имя</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group animate delay-2">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group animate delay-1">
                    <label for="problem">Тип проблемы</label>
                    <select id="problem" name="problem">
                        <option value="diagnostics">Диагностика</option>
                        <option value="cleaning">Чистка от пыли</option>
                        <option value="components">Замена комплектующих</option>
                        <option value="os">Установка ОС</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                <button type="submit" class="btn animate delay-2">Отправить заявку</button>
            </form>
        </div>
    </section> -->

    <!-- Отзывы -->
     
    <section id="testimonials" class="testimonials">
        <div class="container">
           <h2 class="section-title">Отзывы клиентов</h2>
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $review): ?>
                <div class="testimonial-card">
                    <p class="testimonial-text">"<?= htmlspecialchars($review['text']) ?>"</p>
                    <div class="testimonial-author">
                        <img src="<?= htmlspecialchars($review['avatar']) ?>" alt="<?= htmlspecialchars($review['name']) ?>" class="author-avatar">
                        <div>
                            <h4><?= htmlspecialchars($review['name']) ?></h4>
                            <div class="stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></div>
                            <div class="testimonial-date"><?= date('d.m.Y', strtotime($review['date'])) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="add-review-btn">
                <button class="btn" id="addReviewBtn">Оставить отзыв</button>
            </div>
        </div>
    </section>

<!-- Подвал -->
<footer id="contacts">
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Smart Service</h3>
                <p>Профессиональный ремонт компьютеров и ноутбуков с 2010 года</p>
            </div>
            <div class="footer-column">
                <h3>Контакты</h3>
                <p>г. Калининград, ул. Техническая, 15</p>
                <p>+7 (495) 123-45-67</p>
                <p>info@smartservice.ru</p>
            </div>
            <div class="footer-column">
                <h3>Режим работы</h3>
                <p>Пн-Пт: 10:00 - 18:30</p>
                <p>Сб: 10:00 - 16:00</p>
                <p>Вс: выходной</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-vk"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>© 2025 Smart. Все права защищены.</p>
        </div>
    </div>
</footer>

<div id="alert" class="alert">Вы успешно вошли!</div>
<!-- Модальное окно входа -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Вход</h2>
        <form id="loginForm" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn">Войти</button>
        </form>
    </div>
</div>

<!-- Модальное окно отзывов -->
<div id="testimonialModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Оставить отзыв</h2>
        <form method="POST" class="testimonial-form">
            <div class="form-group">
                <label for="name">Ваше имя:</label>
                <input type="text" id="review-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="avatar">Ссылка на аватар (необязательно):</label>
                <input type="url" id="avatar" name="avatar">
            </div>
            <div class="form-group">
                <label for="text">Текст отзыва:</label>
                <textarea id="text" name="text" required></textarea>
            </div>
            <div class="form-group">
                <label>Оценка:</label>
                <div class="rating-stars">
                    <input type="radio" id="star5" name="rating" value="5" checked>
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1">★</label>
                </div>
            </div>
            <button type="submit" name="submit_testimonial" class="btn">Отправить отзыв</button>
        </form>
    </div>
</div>
<!-- Кнопка для открытия модального окна -->
<!-- <button id="myBtn">Войти</button> -->



<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хешируем пароль

    // Подключение к базе данных
    $conn = new mysqli('localhost', 'root', 'root', 'my_database');

    // Проверка соединения
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Вставка данных в таблицу
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "<script>document.getElementById('myBtn').textContent='Выйти';</script>";
        
        echo "<script>alert('Вы успешно вошли!');</script>";
        
      } else {
          echo "<script>alert('Ошибка при входе: ".$stmt->error."');</script>";
      }

      $stmt->close();
      $conn->close();
}
?>

<script src="script.js"></script>
</body>
</html>