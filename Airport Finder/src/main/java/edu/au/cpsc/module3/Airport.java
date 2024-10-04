package edu.au.cpsc.module3;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

public class Airport {
    private String ident;
    private String type;
    private String name;
    private Integer elevationFt;
    private String continent;
    private String isoCountry;
    private String isoRegion;
    private String municipality;
    private String gpsCode;
    private String iataCode;
    private String localCode;
    private Double latitude;
    private Double longitude;

    public static List<Airport> readAll() throws IOException {
        List<Airport> airports = new ArrayList<>();
        InputStream is = Airport.class.getResourceAsStream("/edu/au/cpsc/module3/airport-codes.csv");
        if (is == null) {
            throw new IOException("Resource not found: /edu/au/cpsc/module3/airport-codes.csv");
        }

        try (BufferedReader reader = new BufferedReader(new InputStreamReader(is))) {
            String line;
            boolean isFirstLine = true;
            while ((line = reader.readLine()) != null) {
                if (isFirstLine) {
                    isFirstLine = false;
                    continue; // Skip header line
                }

                String[] fields = line.split(",");
                if (fields.length < 13) {
                    continue; // Adjust if your file has more columns
                }

                Airport airport = new Airport();
                airport.setIdent(fields[0].isEmpty() ? null : fields[0]);
                airport.setType(fields[1].isEmpty() ? null : fields[1]);
                airport.setName(fields[2].isEmpty() ? null : fields[2]);
                airport.setElevationFt(fields[3].isEmpty() ? null : Integer.parseInt(fields[3]));
                airport.setContinent(fields[4].isEmpty() ? null : fields[4]);
                airport.setIsoCountry(fields[5].isEmpty() ? null : fields[5]);
                airport.setIsoRegion(fields[6].isEmpty() ? null : fields[6]);
                airport.setMunicipality(fields[7].isEmpty() ? null : fields[7]);
                airport.setGpsCode(fields[8].isEmpty() ? null : fields[8]);
                airport.setIataCode(fields[9].isEmpty() ? null : fields[9]);
                airport.setLocalCode(fields[10].isEmpty() ? null : fields[10]);

                // Handle coordinates from separate columns
                String longitudeStr = fields[11].trim();
                String latitudeStr = fields[12].trim();
                try {
                    Double longitude = longitudeStr.isEmpty() ? null : Double.parseDouble(longitudeStr);
                    Double latitude = latitudeStr.isEmpty() ? null : Double.parseDouble(latitudeStr);
                    airport.setLongitude(longitude);
                    airport.setLatitude(latitude);
                } catch (NumberFormatException e) {
                    System.err.println("Invalid number format for coordinates: " + fields[11] + ", " + fields[12]);
                    airport.setLongitude(null);
                    airport.setLatitude(null);
                }

                airports.add(airport);
            }
        }
        return airports;
    }


    public String getIdent() {
        return ident;
    }

    public void setIdent(String ident) {
        this.ident = ident;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Integer getElevationFt() {
        return elevationFt;
    }

    public void setElevationFt(Integer elevationFt) {
        this.elevationFt = elevationFt;
    }

    public String getContinent() {
        return continent;
    }

    public void setContinent(String continent) {
        this.continent = continent;
    }

    public String getIsoCountry() {
        return isoCountry;
    }

    public void setIsoCountry(String isoCountry) {
        this.isoCountry = isoCountry;
    }

    public String getIsoRegion() {
        return isoRegion;
    }

    public void setIsoRegion(String isoRegion) {
        this.isoRegion = isoRegion;
    }

    public String getLocalCode() {
        return localCode;
    }

    public void setLocalCode(String localCode) {
        this.localCode = localCode;
    }

    public String getIataCode() {
        return iataCode;
    }

    public void setIataCode(String iataCode) {
        this.iataCode = iataCode;
    }

    public String getGpsCode() {
        return gpsCode;
    }

    public void setGpsCode(String gpsCode) {
        this.gpsCode = gpsCode;
    }

    public String getMunicipality() {
        return municipality;
    }

    public void setMunicipality(String municipality) {
        this.municipality = municipality;
    }

    public Double getLatitude() {
        return latitude;
    }

    public void setLatitude(Double latitude) {
        this.latitude = latitude;
    }

    public Double getLongitude() {
        return longitude;
    }

    public void setLongitude(Double longitude) {
        this.longitude = longitude;
    }
}