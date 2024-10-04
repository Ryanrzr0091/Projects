module edu.au.cpsc.module7 {
    requires javafx.controls;
    requires javafx.fxml;


    opens edu.au.cpsc.module7 to javafx.fxml;
    exports edu.au.cpsc.module7;
    opens edu.au.cpsc.module7.data to javafx.fxml;
    exports edu.au.cpsc.module7.data;
}