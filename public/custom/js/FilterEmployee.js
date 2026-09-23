document.addEventListener('DOMContentLoaded', function () {
    const company = document.querySelector('[name="company_id"]');
    const division = document.querySelector('[name="division_id"]');

    if (!company || !division) return;

    // Simpan semua option Division
    const allDivisions = Array.from(division.options);

    // Awalnya Division disabled
    division.disabled = true;

    company.addEventListener('change', function () {
        const companyId = this.value;

        // Reset Division
        division.innerHTML = '';

        // Jika Company belum dipilih
        if (!companyId) {
            division.disabled = true;

            // Tambahkan option default
            const defaultOption = allDivisions.find(
                option => option.value === ''
            );

            if (defaultOption) {
                division.appendChild(defaultOption.cloneNode(true));
            }

            return;
        }

        // Company sudah dipilih
        division.disabled = false;

        // Tampilkan hanya Division milik Company
        allDivisions.forEach(function (option) {
            if (
                option.value === '' ||
                option.dataset.companyId === companyId
            ) {
                division.appendChild(option.cloneNode(true));
            }
        });
    });
});
