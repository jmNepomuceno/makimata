$(document).ready(function () {
    // --- Load Regions on page load ---
    $.ajax({
        url: '../assets/php/get_regions.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let options = "<option value=''>Select Region</option>";
            $.each(data, function (i, item) {
                options += `<option value="${item.region_code}">${item.region_description}</option>`;
            });
            $('#region').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error loading regions: " + error);
        }
    });

    // --- Load Provinces based on Region ---
    $('#region').on("change", function () {
        let regionCode = $(this).val();
        if (regionCode) {
            $.getJSON("../assets/php/get_provinces.php", { region_code: regionCode }, function (data) {
                let options = "<option value=''>Select Province</option>";
                $.each(data, function (i, item) {
                    options += `<option value="${item.province_code}">${item.province_description}</option>`;
                });
                $('#province').html(options);
                $('#city').html("<option value=''>Select City</option>");
                $('#barangay').html("<option value=''>Select Barangay</option>");
            });
        } else {
            $('#province').html("<option value=''>Select Province</option>");
            $('#city').html("<option value=''>Select City</option>");
            $('#barangay').html("<option value=''>Select Barangay</option>");
        }
    });

    // --- Load Cities based on Province ---
    $('#province').on("change", function () {
        let provinceCode = $(this).val();
        if (provinceCode) {
            $.getJSON("../assets/php/get_cities.php", { province_code: provinceCode }, function (data) {
                let options = "<option value=''>Select City</option>";
                $.each(data, function (i, item) {
                    options += `<option value="${item.municipality_code}">${item.municipality_description}</option>`;
                });
                $('#city').html(options);
                $('#barangay').html("<option value=''>Select Barangay</option>");
            });
        } else {
            $('#city').html("<option value=''>Select City</option>");
            $('#barangay').html("<option value=''>Select Barangay</option>");
        }
    });

    // --- Load Barangays based on City ---
    $('#city').on("change", function () {
        let cityCode = $(this).val();
        if (cityCode) {
            $.getJSON("../assets/php/get_barangays.php", { municipality_code: cityCode }, function (data) {
                let options = "<option value=''>Select Barangay</option>";
                $.each(data, function (i, item) {
                    options += `<option value="${item.barangay_code}">${item.barangay_description}</option>`;
                });
                $('#barangay').html(options);
            });
        } else {
            $('#barangay').html("<option value=''>Select Barangay</option>");
        }
    });
});
