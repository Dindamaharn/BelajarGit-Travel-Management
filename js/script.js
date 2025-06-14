
document.addEventListener('DOMContentLoaded', function () {
    const onewayBtn = document.getElementById('oneway-btn');
    const roundtripBtn = document.getElementById('roundtrip-btn');
    const formOneway = document.getElementById('form-oneway');
    const formRoundtrip = document.getElementById('form-roundtrip');

    function showOnewayForm() {
        onewayBtn.classList.add('active');
        roundtripBtn.classList.remove('active');
        formOneway.style.display = 'flex';
        formRoundtrip.style.display = 'none';
        localStorage.setItem('activeTab', 'oneway');
    }

    function showRoundtripForm() {
        roundtripBtn.classList.add('active');
        onewayBtn.classList.remove('active');
        formOneway.style.display = 'none';
        formRoundtrip.style.display = 'flex';
        localStorage.setItem('activeTab', 'roundtrip');
    }

    onewayBtn.addEventListener('click', showOnewayForm);
    roundtripBtn.addEventListener('click', showRoundtripForm);

    // Baca status tab terakhir dari localStorage
    const savedTab = localStorage.getItem('activeTab');
    if (savedTab === 'roundtrip') {
        showRoundtripForm();
    } else {
        showOnewayForm(); // default
    }
});

// cara bayar

document.querySelectorAll('.payment-item').forEach(item => {
    const arrow = item.querySelector('.toggle-arrow');
    arrow.addEventListener('click', () => {
        item.classList.toggle('open');
    });
});

// tanggal pp
document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#display-range", {
        mode: "range",
        dateFormat: "D, j M",
        minDate: "today",
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                const depart = selectedDates[0];
                const ret = selectedDates[1];
                const formatDate = d =>
                    d.toLocaleDateString("en-GB", {
                        weekday: "short",
                        day: "numeric",
                        month: "short",
                    });

                // tampilkan gabungan di input tampilan
                document.getElementById("display-range").value = `${formatDate(depart)} - ${formatDate(ret)}`;

                // simpan ke input tersembunyi
                document.getElementById("departure-date").value = depart.toISOString().split("T")[0];
                document.getElementById("return-date").value = ret.toISOString().split("T")[0];
            }
        },
    });
});

// search pembayaran
// Ambil elemen input dan semua payment-item
const searchInput = document.getElementById("searchInput");
const paymentItems = document.querySelectorAll(".payment-item");

// Jalankan filter saat user mengetik
searchInput.addEventListener("keyup", function () {
    const filter = searchInput.value.toLowerCase();

    paymentItems.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? "block" : "none";
    });
});

// dropdown
document.addEventListener("DOMContentLoaded", function () {
    const dropdownBtn = document.querySelector(".dropdown-btn");
    const dropdownContent = document.querySelector(".dropdown-content");

    if (dropdownBtn && dropdownContent) {
        dropdownBtn.addEventListener("click", function (e) {
            e.stopPropagation(); // agar tidak langsung tertutup
            dropdownContent.classList.toggle("show");
        });

        document.addEventListener("click", function () {
            dropdownContent.classList.remove("show");
        });
    }
});
