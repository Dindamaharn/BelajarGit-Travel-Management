
document.addEventListener('DOMContentLoaded', function () {
    const onewayBtn = document.getElementById('oneway-btn');
    const roundtripBtn = document.getElementById('roundtrip-btn');
    const formOneway = document.getElementById('form-oneway');
    const formRoundtrip = document.getElementById('form-roundtrip');

    onewayBtn.addEventListener('click', () => {
        onewayBtn.classList.add('active');
        roundtripBtn.classList.remove('active');
        formOneway.style.display = 'flex';
        formRoundtrip.style.display = 'none';
    });

    roundtripBtn.addEventListener('click', () => {
        roundtripBtn.classList.add('active');
        onewayBtn.classList.remove('active');
        formOneway.style.display = 'none';
        formRoundtrip.style.display = 'flex';
    });
});
