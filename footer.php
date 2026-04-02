<footer id="footer" class="custom-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-6">
                <h3 class="text-warning mb-3 fs-2">🏨 <?= getenv('SITE_NAME') ?: 'Golden Stay' ?></h3>
                <p class="mb-2 fs-5">🌟 Премиальный гостиничный комплекс</p>
                <p class="mb-2 fs-5">📍 г. Сочи, ул. Морская, 15</p>
                <p class="mb-0 fs-5">⭐ Рейтинг: 4.8 ★</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h3 class="text-warning mb-3 fs-2">📞 Контакты</h3>
                <p class="mb-2 fs-5">
                    <a href="tel:+74957778899" class="contact-link">
                        <i class="bi bi-telephone-fill"></i> +7 (495) 777-88-99
                    </a>
                </p>
                <p class="mb-2 fs-5">
                    <a href="mailto:<?= getenv('SITE_EMAIL') ?: 'info@goldenstay.ru' ?>" class="contact-link">
                        <i class="bi bi-envelope-fill"></i> <?= getenv('SITE_EMAIL') ?: 'info@goldenstay.ru' ?>
                    </a>
                </p>
                <p class="mb-0 fs-5">🕐 Работаем: круглосуточно</p>
            </div>
        </div>
        <div class="border-top border-secondary mt-4 pt-4 text-center">
            <p class="mb-0">© 2026 <?= getenv('SITE_NAME') ?: 'Golden Stay' ?> — гостиничный комплекс, 5 корпусов у моря. Все права защищены.</p>
        </div>
    </div>
</footer>

<style>
.custom-footer {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color: #ccc;
    margin-top: 3rem;
}
.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #ccc;
    text-decoration: none;
    transition: 0.2s;
}
.contact-link:hover {
    color: #f7c948;
}
@media (max-width: 768px) {
    .custom-footer .text-md-end {
        text-align: center !important;
    }
}
</style>