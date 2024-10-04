package edu.au.cpsc.module3;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;
import javafx.scene.web.WebView;

import java.io.IOException;
import java.io.InputStream;
import java.util.List;
import java.util.Optional;

public class AirportController {

    @FXML
    private TextField identField, iataField, localField;
    @FXML
    private TextField typeField, nameField, elevationField, countryField, regionField, municipalityField;
    @FXML
    private WebView mapView;

    private List<Airport> airports;

    @FXML
    public void initialize() {
        try {
            // Load airport data
            airports = Airport.readAll();
        } catch (IOException e) {
            System.err.println("Error loading airport data: " + e.getMessage());
            e.printStackTrace();
        }
    }

    @FXML
    private void handleSearch() {
        String ident = identField.getText().trim();
        String iataCode = iataField.getText().trim();
        String localCode = localField.getText().trim();

        Optional<Airport> result = airports.stream()
                .filter(a -> (!ident.isEmpty() && a.getIdent().equalsIgnoreCase(ident)) ||
                        (!iataCode.isEmpty() && a.getIataCode().equalsIgnoreCase(iataCode)) ||
                        (!localCode.isEmpty() && a.getLocalCode().equalsIgnoreCase(localCode)))
                .findFirst();

        if (result.isPresent()) {
            updateFields(result.get());
        } else {
            clearFields();
        }
    }

    private void updateFields(Airport airport) {
        typeField.setText(airport.getType() != null ? airport.getType() : "");
        nameField.setText(airport.getName() != null ? airport.getName() : "");
        elevationField.setText(airport.getElevationFt() != null ? String.valueOf(airport.getElevationFt()) : "");
        countryField.setText(airport.getIsoCountry() != null ? airport.getIsoCountry() : "");
        regionField.setText(airport.getIsoRegion() != null ? airport.getIsoRegion() : "");
        municipalityField.setText(airport.getMunicipality() != null ? airport.getMunicipality() : "");
        updateMapView(airport.getLatitude(), airport.getLongitude());
    }
    private void updateMapView(Double latitude, Double longitude) {
        if (latitude != null && longitude != null) {
            String url = String.format("https://www.windy.com/?%f,%f,12", latitude, longitude);
            mapView.getEngine().load(url);
        } else {
            mapView.getEngine().load("about:blank");
        }
    }

    private void clearFields() {
        typeField.clear();
        nameField.clear();
        elevationField.clear();
        countryField.clear();
        regionField.clear();
        municipalityField.clear();
        mapView.getEngine().load("about:blank");
    }
}
