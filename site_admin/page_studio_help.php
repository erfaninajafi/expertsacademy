<?php
include_once './includer.php';
$Tabpage = '58';
$pagetitle = 'راهنمای Page Studio';
include_once './header.inc.php';
?>
<style>
    .psh-hero {
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 55%, #ecfdf5 100%);
        border: 1px solid #e2e8f0;
    }
    .psh-hero .psh-badge {
        background: #fff;
        border: 1px solid #c7d2fe;
        color: #4338ca;
        font-weight: 600;
    }
    .psh-toc {
        position: sticky;
        top: 1rem;
    }
    .psh-toc a {
        display: block;
        padding: .45rem .7rem;
        border-radius: .6rem;
        color: #475569;
        text-decoration: none;
        font-size: .86rem;
    }
    .psh-toc a:hover,
    .psh-toc a:focus {
        background: #eef2ff;
        color: #3730a3;
    }
    .psh-step {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        height: 100%;
    }
    .psh-step-number {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #eef2ff;
        color: #3730a3;
        font-weight: 700;
    }
    .psh-card {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        height: 100%;
    }
    .psh-card h3 {
        font-size: 1rem;
    }
    .psh-muted {
        color: #64748b;
    }
    .psh-kbd {
        display: inline-block;
        padding: .1rem .4rem;
        border: 1px solid #cbd5e1;
        border-bottom-width: 2px;
        border-radius: .35rem;
        background: #f8fafc;
        font-size: .78rem;
        direction: ltr;
    }
    .psh-section {
        scroll-margin-top: 1.25rem;
    }
    .psh-role-table th,
    .psh-role-table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    @media (max-width: 991.98px) {
        .psh-toc {
            position: static;
        }
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="psh-hero p-4 p-lg-5 mb-4">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div class="pe-lg-4">
                    <span class="badge psh-badge mb-2">راهنمای کاربری</span>
                    <h1 class="h3 mb-2">راهنمای Page Studio</h1>
                    <p class="mb-2 psh-muted">
                        این راهنما گردش‌کار ساخت صفحه، ویرایش محتوا، پیش‌نمایش، انتشار، مدیریت نسخه‌ها،
                        سیستم طراحی و نقش‌های کاربری را توضیح می‌دهد.
                    </p>
                    <p class="mb-0 psh-muted small">
                        لازم نیست برای استفاده از Page Studio چت یا مستندات بیرونی را باز کنید؛ همین صفحه
                        مرجع کاری شماست.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="page_studio.php" class="btn btn-primary">رفتن به Page Studio</a>
                    <a href="help.php" class="btn btn-outline-secondary">راهنمای قسمت‌های سایت</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3">
                <div class="psh-toc card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="fw-semibold mb-2 px-2">فهرست این راهنما</div>
                        <a href="#quick-start">شروع سریع</a>
                        <a href="#workflow">گردش‌کار کامل</a>
                        <a href="#scenarios">سناریوهای پشتیبانی‌شده</a>
                        <a href="#states">وضعیت‌های صفحه</a>
                        <a href="#roles">نقش‌ها و دسترسی‌ها</a>
                        <a href="#other-panel">بخش‌های دیگر پنل</a>
                        <a href="#notes">نکات مهم و خطاهای رایج</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <section id="quick-start" class="psh-section mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 mb-0">شروع سریع</h2>
                        <span class="badge bg-label-primary">۶ قدم</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۱</span>
                                    <strong>صفحه را انتخاب کنید</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    از منوی «صفحه» یک صفحهٔ موجود را باز کنید یا با «صفحهٔ جدید» پیش‌نویس بسازید.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۲</span>
                                    <strong>محتوا را بچینید</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    از کتابخانه کامپوننت اضافه کنید، متن را مستقیم روی بوم ویرایش کنید و
                                    تنظیمات را در بازرس تغییر دهید.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۳</span>
                                    <strong>ذخیره کنید</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    با دکمه «ذخیره» یا <span class="psh-kbd">Ctrl/Cmd + S</span> یک نسخهٔ
                                    جدید در تاریخچه ساخته می‌شود.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۴</span>
                                    <strong>پیش‌نمایش بگیرید</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    صفحه را در عرض موبایل، تبلت و دسکتاپ ببینید؛ پیش‌نمایش از همان رندر
                                    نسخهٔ عمومی استفاده می‌کند.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۵</span>
                                    <strong>مسیر و انتشار</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    آدرس عمومی را مشخص کنید، بررسی‌های پیش‌ازانتشار را ببینید و پس از رفع خطاها
                                    صفحه را با تأیید نهایی منتشر کنید.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="psh-step p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="psh-step-number">۶</span>
                                    <strong>خروجی را بررسی کنید</strong>
                                </div>
                                <p class="mb-0 small psh-muted">
                                    نشانی عمومی را باز کنید و مطمئن شوید محتوای جدید جای محتوای قبلی همان
                                    کد نشسته است.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="workflow" class="psh-section mb-4">
                    <h2 class="h5 mb-3">گردش‌کار کامل</h2>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6">۱. انتخاب یا ساخت صفحه</h3>
                            <ul class="mb-0 small psh-muted">
                                <li>برای ویرایش صفحهٔ موجود، آن را از منوی «صفحه» انتخاب کنید.</li>
                                <li>در «صفحهٔ جدید»، عنوان، زبان (فارسی یا انگلیسی) و آدرس عمومی را وارد کنید.</li>
                                <li>اگر آدرس عمومی را وارد نکنید، می‌توانید همچنان صفحه را به‌صورت پیش‌نویس
                                    بسازید و پیش‌نمایش بگیرید.</li>
                                <li>اگر تغییرات ذخیره‌نشده داشته باشید، پیش از عوض کردن صفحه تأیید گرفته
                                    می‌شود.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6">۲. افزودن و ویرایش محتوا</h3>
                            <div class="row g-3 small psh-muted">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li>کتابخانهٔ کامپوننت‌ها را جست‌وجو یا بر اساس دسته فیلتر کنید.</li>
                                        <li>کامپوننت را به بوم اضافه کنید و گونهٔ دلخواه را انتخاب کنید.</li>
                                        <li>متن را مستقیم روی بوم یا از بازرس ویرایش کنید.</li>
                                        <li>بخش‌ها و بلوک‌ها را جابه‌جا، تکرار یا حذف کنید.</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li>در «لایه‌ها» ساختار صفحه و ترتیب عناصر را ببینید.</li>
                                        <li>نمایش هر کامپوننت را برای دسکتاپ، تبلت یا موبایل خاموش کنید.</li>
                                        <li>تراز، لحن، فاصله، حرکت و دسترس‌پذیری را در بازرس تنظیم کنید.</li>
                                        <li>با <span class="psh-kbd">Ctrl/Cmd + Z</span> واگرد و با
                                            <span class="psh-kbd">Ctrl/Cmd + Y</span> ازنو کنید.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6">۳. ذخیره و تاریخچه</h3>
                            <ul class="mb-0 small psh-muted">
                                <li>هر ذخیره یک نسخهٔ جدید و تغییرناپذیر می‌سازد؛ نسخهٔ قبلی حذف نمی‌شود.</li>
                                <li>در «تاریخچه» می‌توانید نسخه‌ها را ببینید و تفاوت ساختاری را با پیش‌نویس
                                    فعلی مقایسه کنید.</li>
                                <li>«بازگردانی» نسخهٔ قدیمی را به‌عنوان پیش‌نویس جدید می‌آورد و چیزی حذف
                                    نمی‌کند.</li>
                                <li>انتشار مستقیم یک نسخهٔ قدیمی، همان rollback است و تاریخچه را بازنویسی
                                    نمی‌کند.</li>
                                <li>اگر مرورگر بسته شود، نسخهٔ محلی ذخیره‌نشده پیشنهاد بازیابی می‌شود؛ با این
                                    حال ذخیرهٔ اصلی را دستی انجام دهید.</li>
                                <li>اگر دو نفر همزمان یک صفحه را ویرایش کنند، ذخیرهٔ نسخهٔ قدیمی رد می‌شود تا
                                    کار دیگری از بین نرود.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6">۴. پیش‌نمایش</h3>
                            <ul class="mb-0 small psh-muted">
                                <li>دکمه «پیش‌نمایش» صفحه را بدون انتشار عمومی نشان می‌دهد.</li>
                                <li>عرض موبایل، تبلت و دسکتاپ قابل انتخاب است.</li>
                                <li>برای ارسال به دیگران، در دیالوگ «انتشار» روی «کپی لینک پیش‌نمایش» بزنید؛
                                    این لینک موقت است و معمولاً پس از حدود یک ساعت منقضی می‌شود.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h3 class="h6">۵. مسیر، بررسی و انتشار</h3>
                            <div class="row g-3 small psh-muted">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>نامک:</strong> مسیر اختیاری و یکتا برای سئو و sitemap.</li>
                                        <li><strong>آدرس عمومی:</strong> مسیر اصلی و مستقل صفحه در سایت.</li>
                                        <li><strong>انتشار:</strong> پس از بررسی کیفیت و تأیید نهایی، نسخه ذخیره‌شده را عمومی می‌کند.</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>لغو انتشار:</strong> صفحه را از دسترس عمومی خارج می‌کند.</li>
                                        <li><strong>آرشیو:</strong> صفحه را از چرخه انتشار خارج می‌کند.</li>
                                        <li>بررسی‌های پیش‌ازانتشار خطاهای بازدارنده و هشدارها را نشان می‌دهد.</li>
                                        <li>خطاهای بازدارنده انتشار را متوقف می‌کنند؛ هشدارها فقط اطلاع‌رسانی
                                            هستند.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="scenarios" class="psh-section mb-4">
                    <h2 class="h5 mb-3">سناریوهای پشتیبانی‌شده</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>مدیریت صفحه</h3>
                                <p class="small psh-muted mb-0">
                                    ساخت صفحهٔ فارسی/انگلیسی، تعیین آدرس عمومی، انتخاب صفحه، هشدار تغییرات
                                    ذخیره‌نشده، ذخیرهٔ نسخه‌دار، بازیابی نسخهٔ محلی، جلوگیری از تعارض
                                    همزمان، حذف/تکرار/جابه‌جایی بخش‌ها و بلوک‌ها، تغییر ترتیب لایه‌ها،
                                    پنهان‌سازی در دستگاه‌ها، ویرایش مستقیم متن، تنظیم طراحی/حرکت/دسترس‌پذیری،
                                    واگرد و ازنو.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>کتابخانهٔ کامپوننت‌ها</h3>
                                <p class="small psh-muted mb-0">
                                    بیش از ۶۰ نوع کامپوننت و گونه‌های متعدد در دسته‌های محتوا، هیرو، رسانه،
                                    اعتماد اجتماعی، تبدیل، مقایسه و قیمت‌گذاری، خط زمانی، فرم‌ها، ناوبری و
                                    فوتر، بنتو و چیدمان.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>مدیریت رسانه</h3>
                                <p class="small psh-muted mb-0">
                                    بارگذاری تصویر، متن جایگزین، جست‌وجو، کپی شناسهٔ رسانه، نقطهٔ کانونی،
                                    انتخاب از کتابخانه، پشتیبانی JPEG/PNG/WebP/AVIF/SVG، حداکثر ۸ مگابایت،
                                    تغییر نام امن و ذخیره بیرون از مسیرهای اجرایی.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>تاریخچه و بازگردانی</h3>
                                <p class="small psh-muted mb-0">
                                    مشاهدهٔ همهٔ نسخه‌ها، تاریخ و شمارهٔ نسخه، مقایسهٔ ساختاری، تعداد بخش‌ها و
                                    تغییرات کامپوننت، بازگردانی به‌عنوان پیش‌نویس جدید و انتشار نسخهٔ قدیمی
                                    به‌عنوان rollback.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>قالب‌ها</h3>
                                <p class="small psh-muted mb-0">
                                    مرور و جست‌وجوی قالب‌ها، فیلتر دسته، علاقه‌مندی، پیش‌نمایش و ساخت صفحهٔ
                                    جدید با عنوان و زبان دلخواه. تغییرات بعدی قالب روی صفحهٔ ساخته‌شده اثر
                                    ندارد.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>سیستم طراحی</h3>
                                <p class="small psh-muted mb-0">
                                    تنظیم رنگ روشن/تیره، کنتراست، فونت لاتین و راست‌به‌چپ، اندازه‌ها،
                                    فاصله‌ها، شبکه، عرض محتوا، نقاط شکست، گردی گوشه‌ها، سایه‌ها، دکمه‌ها،
                                    پس‌زمینه و گرادیان، حرکت، مقیاس RTL و پیش‌نمایش زنده؛ سپس انتشار پوسته.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>استودیوی کامپوننت</h3>
                                <p class="small psh-muted mb-0">
                                    مرور ۶۰ نوع کامپوننت، جست‌وجو و فیلتر، پیش‌نمایش LTR/RTL در سه عرض،
                                    مشاهدهٔ قرارداد و ویژگی‌ها، ویرایش فراداده و مستندات، پیش‌تنظیم‌ها و
                                    تاریخچهٔ نسخه‌ها.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>سئو</h3>
                                <p class="small psh-muted mb-0">
                                    عنوان، توضیحات، کلیدواژه‌ها، canonical، robots و تصویر Open Graph.
                                    sitemap از صفحات منتشرشدهٔ قابل ایندکس و نشانی‌های عمومی ساخته می‌شود.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="states" class="psh-section mb-4">
                    <h2 class="h5 mb-3">وضعیت‌های صفحه</h2>
                    <div class="table-responsive card border-0 shadow-sm">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>وضعیت</th>
                                    <th>معنی</th>
                                    <th>خروجی عمومی</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-label-secondary">پیش‌نویس</span></td>
                                    <td>نسخهٔ کاری قابل ویرایش</td>
                                    <td>ندارد</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-label-success">منتشرشده</span></td>
                                    <td>نسخهٔ فعال عمومی</td>
                                    <td>دارد، اگر آدرس عمومی ثبت شده باشد</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-label-danger">لغو انتشار</span></td>
                                    <td>عمداً از دسترس خارج شده</td>
                                    <td>ندارد</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-label-dark">آرشیو</span></td>
                                    <td>بازنشسته شده</td>
                                    <td>ندارد</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="roles" class="psh-section mb-4">
                    <h2 class="h5 mb-3">نقش‌ها و سناریوی هر کاربر</h2>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>مدیر</h3>
                                <p class="small psh-muted mb-0">
                                    همهٔ کارها، از جمله مدیریت کاربران و نقش‌ها.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>توسعه‌دهنده</h3>
                                <p class="small psh-muted mb-0">
                                    ساخت، ویرایش، بازگردانی، مسیر، انتشار، طراحی، کامپوننت‌ها، قالب‌ها،
                                    رسانه، سئو، تغییرمسیرها و گزارش رویدادها.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>طراح</h3>
                                <p class="small psh-muted mb-0">
                                    ویرایش صفحه، سیستم طراحی، مدیریت کامپوننت‌ها، رسانه و پیش‌نمایش؛ بدون
                                    انتشار.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>ویرایشگر</h3>
                                <p class="small psh-muted mb-0">
                                    ساخت و ویرایش صفحه، بازگردانی، مسیر، رسانه و سئو؛ بدون انتشار و بدون
                                    طراحی.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>بازبین</h3>
                                <p class="small psh-muted mb-0">
                                    پیش‌نمایش پیش‌نویس و مشاهدهٔ گزارش رویدادها.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="psh-card p-3">
                                <h3>منتشرکننده</h3>
                                <p class="small psh-muted mb-0">
                                    انتشار، لغو انتشار، آرشیو، بازگردانی، پیش‌نمایش و گزارش رویدادها؛
                                    بدون ویرایش محتوا.
                                </p>
                            </div>
                        </div>
                    </div>

                    <details class="card border-0 shadow-sm">
                        <summary class="card-body fw-semibold" style="cursor:pointer">
                            مشاهدهٔ جدول کامل دسترسی‌ها
                        </summary>
                        <div class="table-responsive px-3 pb-3">
                            <table class="table table-sm psh-role-table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>دسترسی</th>
                                        <th>مدیر</th>
                                        <th>توسعه‌دهنده</th>
                                        <th>طراح</th>
                                        <th>ویرایشگر</th>
                                        <th>بازبین</th>
                                        <th>منتشرکننده</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pshRoles = [
                                        'pages.create' => ['مدیر', 'توسعه‌دهنده', 'ویرایشگر'],
                                        'pages.edit' => ['مدیر', 'توسعه‌دهنده', 'طراح', 'ویرایشگر'],
                                        'pages.restore' => ['مدیر', 'توسعه‌دهنده', 'ویرایشگر', 'منتشرکننده'],
                                        'pages.route' => ['مدیر', 'توسعه‌دهنده', 'ویرایشگر'],
                                        'pages.publish' => ['مدیر', 'توسعه‌دهنده', 'منتشرکننده'],
                                        'pages.unpublish' => ['مدیر', 'توسعه‌دهنده', 'منتشرکننده'],
                                        'pages.archive' => ['مدیر', 'توسعه‌دهنده', 'منتشرکننده'],
                                        'design.edit' => ['مدیر', 'توسعه‌دهنده', 'طراح'],
                                        'components.manage' => ['مدیر', 'توسعه‌دهنده', 'طراح'],
                                        'templates.manage' => ['مدیر', 'توسعه‌دهنده'],
                                        'media.upload' => ['مدیر', 'توسعه‌دهنده', 'طراح', 'ویرایشگر'],
                                        'media.manage' => ['مدیر', 'توسعه‌دهنده', 'طراح'],
                                        'preview.draft' => ['مدیر', 'توسعه‌دهنده', 'طراح', 'ویرایشگر', 'بازبین', 'منتشرکننده'],
                                        'seo.manage' => ['مدیر', 'توسعه‌دهنده', 'ویرایشگر'],
                                        'redirects.manage' => ['مدیر', 'توسعه‌دهنده'],
                                        'users.manage' => ['مدیر'],
                                        'audit.read' => ['مدیر', 'توسعه‌دهنده', 'بازبین', 'منتشرکننده'],
                                    ];
                                    $pshRoleNames = ['مدیر', 'توسعه‌دهنده', 'طراح', 'ویرایشگر', 'بازبین', 'منتشرکننده'];
                                    $pshPermissionNames = [
                                        'pages.create' => 'ساخت صفحه',
                                        'pages.edit' => 'ویرایش صفحه',
                                        'pages.restore' => 'بازگردانی صفحه',
                                        'pages.route' => 'مسیر صفحه',
                                        'pages.publish' => 'انتشار صفحه',
                                        'pages.unpublish' => 'لغو انتشار صفحه',
                                        'pages.archive' => 'آرشیو صفحه',
                                        'design.edit' => 'ویرایش طراحی',
                                        'components.manage' => 'مدیریت کامپوننت‌ها',
                                        'templates.manage' => 'مدیریت قالب‌ها',
                                        'media.upload' => 'بارگذاری رسانه',
                                        'media.manage' => 'مدیریت رسانه',
                                        'preview.draft' => 'پیش‌نمایش پیش‌نویس',
                                        'seo.manage' => 'مدیریت سئو',
                                        'redirects.manage' => 'مدیریت تغییرمسیرها',
                                        'users.manage' => 'مدیریت کاربران',
                                        'audit.read' => 'مشاهدهٔ گزارش رویدادها',
                                    ];
                                    foreach ($pshRoles as $pshPermission => $pshAllowed):
                                    ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($pshPermissionNames[$pshPermission], ENT_QUOTES, 'UTF-8') ?></td>
                                        <?php foreach ($pshRoleNames as $pshRole): ?>
                                            <td>
                                                <?php if (in_array($pshRole, $pshAllowed, true)): ?>
                                                    <span class="text-success" aria-label="دارد">✓</span>
                                                <?php else: ?>
                                                    <span class="text-muted" aria-label="ندارد">—</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                    <p class="small psh-muted mt-2 mb-0">
                        اگر دکمه‌ای را نمی‌بینید، احتمالاً نقش کاربری آن دسترسی را ندارد. این محدودیت فقط در
                        رابط کاربری نیست؛ API هم درخواست بدون دسترسی را رد می‌کند.
                    </p>
                </section>

                <section id="other-panel" class="psh-section mb-4">
                    <h2 class="h5 mb-3">بخش‌های دیگر پنل</h2>
                    <div class="table-responsive card border-0 shadow-sm">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>بخش</th>
                                    <th>کاربرد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>داشبورد</td><td>نمای کلی پنل</td></tr>
                                <tr><td>طراحی صفحات</td><td>صفحه‌ساز قدیمی و ساختار صفحه‌های CMS</td></tr>
                                <tr><td>صفحات متنی</td><td>مدیریت صفحه‌های متنی ساده</td></tr>
                                <tr><td>راهنمای قسمت‌ها</td><td>راهنمای بخش‌های قدیمی سایت</td></tr>
                                <tr><td>منوی بالای سایت / منوی پایین سایت</td><td>مدیریت ناوبری اصلی و فوتر</td></tr>
                                <tr><td>فرم‌ها و درخواست‌ها</td><td>پیام‌های تماس، همکاری، پیوستن به ما، تماس سریع و مشاوره</td></tr>
                                <tr><td>کاربران پنل</td><td>ساخت و مدیریت کاربران مدیر</td></tr>
                                <tr><td>زبان فارسی / انگلیسی</td><td>مدیریت متن‌های زبان سایت</td></tr>
                                <tr><td>تنظیمات عمومی</td><td>تنظیمات کلی سایت</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small psh-muted mt-2 mb-0">
                        Page Studio فقط محتوای صفحه را می‌سازد. هدر، فوتر و منوها همچنان از بخش‌های قدیمی
                        پنل مدیریت می‌شوند.
                    </p>
                </section>

                <section id="notes" class="psh-section mb-4">
                    <h2 class="h5 mb-3">نکات مهم و خطاهای رایج</h2>

                    <div class="alert alert-warning">
                        <div class="fw-semibold mb-1">صفحه بدون آدرس عمومی منتشر نمی‌شود</div>
                        <div class="small">در پنجره «بررسی و انتشار»، از تنظیمات مسیر صفحه آدرس عمومی را ثبت کنید.</div>
                    </div>


                    <div class="alert alert-secondary">
                        <div class="fw-semibold mb-1">پوستهٔ جدید روی صفحه‌های قدیمی خودکار اعمال نمی‌شود</div>
                        <div class="small">
                            پس از «انتشار پوسته»، صفحه‌های قبلی تا زمانی که دوباره منتشر نشوند، توکن‌های
                            ذخیره‌شده در نسخهٔ خودشان را نگه می‌دارند.
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6">پرسش‌های رایج</h3>
                            <dl class="row small mb-0">
                                <dt class="col-sm-4">چرا صفحهٔ من در سایت دیده نمی‌شود؟</dt>
                                <dd class="col-sm-8 psh-muted">
                                    ممکن است صفحه منتشر نشده یا آدرس عمومی آن ثبت نشده باشد. وضعیت و مسیر
                                    را در پنجره «بررسی و انتشار» بررسی کنید.
                                </dd>
                                <dt class="col-sm-4">چرا دکمهٔ انتشار غیرفعال است؟</dt>
                                <dd class="col-sm-8 psh-muted">
                                    ممکن است نقش شما دسترسی انتشار نداشته باشد یا بررسی‌های پیش‌ازانتشار خطای
                                    بازدارنده پیدا کرده باشند.
                                </dd>
                                <dt class="col-sm-4">چطور یک تغییر را برگردانم؟</dt>
                                <dd class="col-sm-8 psh-muted">
                                    از «تاریخچه» نسخهٔ قبلی را بازگردانی کنید تا به‌عنوان پیش‌نویس جدید
                                    بیاید، یا همان نسخه را مستقیم منتشر کنید.
                                </dd>
                                <dt class="col-sm-4">تفاوت «بازگردانی» و «انتشار نسخهٔ قدیمی» چیست؟</dt>
                                <dd class="col-sm-8 psh-muted">
                                    بازگردانی یک پیش‌نویس جدید می‌سازد؛ انتشار نسخهٔ قدیمی همان لحظه آن نسخه
                                    را عمومی می‌کند. هیچ‌کدام تاریخچه را حذف نمی‌کنند.
                                </dd>
                                <dt class="col-sm-4">چرا پیش‌نمایش با سایت یکی است؟</dt>
                                <dd class="col-sm-8 psh-muted">
                                    هر دو از یک رندر PHP استفاده می‌کنند؛ بنابراین پیش‌نمایش نمی‌تواند از
                                    خروجی واقعی جدا شود.
                                </dd>
                            </dl>
                        </div>
                    </div>
                </section>

                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="page_studio.php" class="btn btn-primary">رفتن به Page Studio</a>
                    <a href="index.php" class="btn btn-outline-secondary">بازگشت به داشبورد</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once './footer.inc.php'; ?>
