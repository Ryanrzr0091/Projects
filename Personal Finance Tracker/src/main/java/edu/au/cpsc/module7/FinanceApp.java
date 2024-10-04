package edu.au.cpsc.module7;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

public class FinanceApp extends Application {
    @Override
    public void start(Stage stage) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(FinanceApp.class.getResource("finance-view.fxml"));
        Scene scene = new Scene(fxmlLoader.load());
        stage.setTitle("Personal Finance Tracker");
        stage.setScene(scene);
        stage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}