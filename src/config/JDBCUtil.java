package config;

import javax.swing.JOptionPane;
import java.sql.Connection;
import java.sql.DriverManager;

public class JDBCUtil {
    public static Connection getConnection(){
        Connection res = null;
        try{
            DriverManager.registerDriver(new com.mysql.jdbc.Driver());
            String url = "jdbc:mySQL://localhost:3306/quanli";
            String userName = "root";
            String password = "";

            res = DriverManager.getConnection(url,userName,password);
        } catch (Exception e){
            JOptionPane.showMessageDialog(null, "Khong the ket noi csdl", "Loi", JOptionPane.ERROR_MESSAGE);
        }
        return res;
    }
    public static void closeConnection(Connection c){
        try {
            if (c != null){
                c.close();
            }
        } catch (Exception e){
            e.printStackTrace();
        }
    }
}