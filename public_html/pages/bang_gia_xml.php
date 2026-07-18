<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="text-uppercase fw-bold text-warning">Bảng Giá Điện Thoại Cập Nhật Nhanh (Dữ liệu XML)</h2>
            <p class="text-muted">Thông tin sản phẩm được đồng bộ thời gian thực từ hệ thống lưu trữ XML bằng công nghệ AJAX</p>
            <button class="btn btn-warning fw-bold text-white shadow-sm" onclick="loadXMLData()">
                <i class="bi bi-arrow-clockwise"></i> Nạp Dữ Liệu XML
            </button>
        </div>
    </div>

    <!-- Khu vực hiển thị bảng dữ liệu -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="xmlTable" style="display:none;">
                            <thead class="table-dark text-uppercase fs-7">
                                <tr>
                                    <th scope="col" class="ps-4">Mã SP</th>
                                    <th scope="col">Tên sản phẩm</th>
                                    <th scope="col">Hãng</th>
                                    <th scope="col">Cấu hình (RAM/ROM)</th>
                                    <th scope="col" class="text-end pe-4">Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody id="xmlContent">
                                <!-- Dữ liệu từ file XML sẽ được chèn vào đây bởi JS -->
                            </tbody>
                        </table>
                        <!-- Trạng thái chờ -->
                        <div id="loadingStatus" class="text-center py-5">
                            <p class="text-muted mb-0">Nhấp vào nút "Nạp Dữ Liệu XML" ở trên để kiểm tra dữ liệu XML DOM...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadXMLData() {
    // 1. Khởi tạo đối tượng XMLHttpRequest để lấy file từ server
    var xhr = new XMLHttpRequest();
    
    // Sử dụng đường dẫn tương đối từ file index.php gọi vào pages/products.xml
    xhr.open("GET", "pages/products.xml", true);
    
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // Nhận kết quả trả về dạng tài liệu XML
            var xmlDoc = this.responseXML;
            if (!xmlDoc) {
                alert("Lỗi: Không thể phân tích cấu trúc file XML!");
                return;
            }

            var tableBody = document.getElementById("xmlContent");
            tableBody.innerHTML = ""; // Xóa dữ liệu cũ trong bảng (nếu có)

            // Lấy ra danh sách tất cả các phần tử <product>
            var products = xmlDoc.getElementsByTagName("product");

            // 2. Duyệt qua từng nút sản phẩm bằng XML DOM
            for (var i = 0; i < products.length; i++) {
                var productNode = products[i];

                // Lấy ID sản phẩm từ thuộc tính của nút
                var id = productNode.getAttribute("id");

                // Sử dụng các nút con (childNodes) để lấy dữ liệu chi tiết
                var childNodes = productNode.childNodes;
                var name = "", brand = "", price = 0, ram = "", rom = "";

                // Duyệt qua các nút con bằng logic nextSibling / firstChild
                var currentChild = productNode.firstChild;
                while (currentChild) {
                    // Kiểm tra xem nút có phải là nút Element (loại 1) không
                    if (currentChild.nodeType === 1) {
                        var nodeName = currentChild.nodeName;
                        var nodeValue = currentChild.textContent || currentChild.innerText;

                        if (nodeName === "name") name = nodeValue;
                        else if (nodeName === "brand") brand = nodeValue;
                        else if (nodeName === "price") price = parseInt(nodeValue);
                        else if (nodeName === "ram") ram = nodeValue;
                        else if (nodeName === "rom") rom = nodeValue;
                    }
                    // Chuyển sang nút tiếp theo (nextSibling)
                    currentChild = currentChild.nextSibling;
                }

                // Định dạng hiển thị tiền tệ VND
                var formattedPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);

                // Tạo dòng bảng HTML mới
                var row = `<tr>
                    <td class="ps-4 fw-bold text-secondary">${id}</td>
                    <td class="fw-bold text-dark">${name}</td>
                    <td><span class="badge bg-light text-dark border">${brand}</span></td>
                    <td>${ram} / ${rom}</td>
                    <td class="text-end pe-4 fw-bold text-danger">${formattedPrice}</td>
                </tr>`;

                // Nạp dòng mới vào bảng
                tableBody.innerHTML += row;
            }

            // Hiển thị bảng dữ liệu và ẩn dòng thông báo trạng thái
            document.getElementById("xmlTable").style.display = "table";
            document.getElementById("loadingStatus").style.display = "none";
        }
    };
    
    // Gửi yêu cầu đi
    xhr.send();
}
</script>