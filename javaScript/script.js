function showMessage(message) {
  alert(message);
}

$(document).ready(function () {
  $("#dataTable").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json", // Sử dụng tiếng Việt
    },
    paging: true, // Hiển thị phân trang
    pageLength: 10, // Số lượng bản ghi trên một trang
    searching: true, // Hiển thị thanh tìm kiếm
    ordering: true, // Cho phép sắp xếp cột
    order: [[0, "asc"]], // Sắp xếp theo cột đầu tiên tăng dần
  });
});
