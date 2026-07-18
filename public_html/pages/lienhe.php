<section class="bg-light py-5">
    <div class="container text-center">

        <h1 class="fw-bold">
            Liên hệ với chúng tôi
        </h1>

        <p class="text-muted">
            Chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc của bạn.
        </p>

    </div>
</section>

<!-- Nội dung liên hệ -->
<section id="contact"
         class="container my-5">

    <div class="row">

        <!-- Thông tin -->
        <div class="col-md-5 mb-4">

            <h3 class="mb-3">
                Thông tin liên hệ
            </h3>

            <p class="text-muted">
                Nếu bạn có bất kỳ câu hỏi nào về sản phẩm hoặc dịch vụ,
                hãy gửi thông tin cho chúng tôi.
            </p>

            <ul class="list-group">

                <li class="list-group-item">
                    📍 Quận Ninh Kiều, TP. Cần Thơ
                </li>

                <li class="list-group-item">
                    📞 0335 935 101
                </li>

                <li class="list-group-item">
                    📧 Hoahuongduongphone@gmail.com
                </li>

            </ul>

        </div>

        <!-- Form -->
        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-body p-4">

                    <form method="post" action="pages/xuly_lienhe.php"> 

                        <div class="mb-3">

                            <label class="form-label">
                                Họ và tên
                            </label>

                            <input type="text"
                                   class="form-control"
                                   placeholder="Nhập họ tên"
                                   name="hoten">
                                    
                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input type="email"
                                   class="form-control"
                                   placeholder="example@gmail.com"
                                   name="email">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Nội dung
                            </label>

                            <textarea class="form-control"
                                      rows="5"
                                      placeholder="Nhập nội dung cần hỗ trợ"
                                      name="noidung"></textarea>

                        </div>

                        <button type="submit"
                                class="btn btn-success w-100">

                            Gửi liên hệ

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>