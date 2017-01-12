var filteredAgencies;

function agency(status) {
    var selectedCountry = $('#country').val();
    if (selectedRegistrationAgency && !status && selectedCountry == country) {
        filteredAgencies = '<option value="' + selectedRegistrationAgency + '" selected="selected">' + agencies[selectedRegistrationAgency] + '</option>';
    } else {
        filteredAgencies = '<option value="" selected="selected">Select an agency</option>';
    }
    for (var i in agencies) {
        if (i.indexOf(selectedCountry) == 0 || i.indexOf('XI') == 0 || i.indexOf('XM') == 0) {
            filteredAgencies += '<option value="' + i + '">' + agencies[i] + '</option>';
        }
    }

    return filteredAgencies;
}

$('#organisationRegistrationAgency')
    .html(agency(false));

$('#country').on('change', function () {
    var agencySelectField = $('#organisationRegistrationAgency');

    filteredAgencies = agency(true);

    agencySelectField.html(filteredAgencies).change();
});

$('#country, #organisationRegistrationAgency, #organisationRegistrationNumber').on('keyup change', function () {
    var selectedRegistrationNumber = $('#organisationRegistrationNumber');
    var selectedRegistrationAgency = $('#organisationRegistrationAgency');
    var selectedCountry = $('#country');
    var identifier = '';

    if (selectedCountry.val() == '' || selectedRegistrationAgency.val() == '' || selectedRegistrationNumber.val() == '') {
    } else {
        identifier = selectedRegistrationAgency.val() + '-' + selectedRegistrationNumber.val();
    }

    $('#organisationIatiIdentifier').val(identifier).trigger('blur');
});
