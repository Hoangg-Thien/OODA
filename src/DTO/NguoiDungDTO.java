package DTO;

import java.util.Objects;

public class NguoiDungDTO {
    private String fullname;
    private String user_name;
    private int sdt;
    private String user_role;
    private String user_status;

    public NguoiDungDTO(){

    }

    public String getFullname() {
        return fullname;
    }

    public void setFullname(String fullname) {
        this.fullname = fullname;
    }

    public String getUser_name() {
        return user_name;
    }

    public void setUser_name(String user_name) {
        this.user_name = user_name;
    }

    public int getSdt() {
        return sdt;
    }

    public void setSdt(int sdt) {
        this.sdt = sdt;
    }

    public String getUser_role() {
        return user_role;
    }

    public void setUser_role(String user_role) {
        this.user_role = user_role;
    }

    public String getUser_status() {
        return user_status;
    }

    public void setUser_status(String user_status) {
        this.user_status = user_status;
    }

    @Override
    public boolean equals(Object obj) {
        if(this == obj){
            return true;
        }
        if(obj == null){
            return false;
        }
        if(getClass() != obj.getClass()){
            return false;
        }
        final NguoiDungDTO other = (NguoiDungDTO) obj;
        if(!Objects.equals(this.fullname,other.fullname)){
            return false;
        }
        if(!Objects.equals(this.user_name,other.user_name)){
            return false;
        }
        if(this.user_role != other.user_role){
            return false;
        }
        if(this.user_status != other.user_status){
            return false;
        }
        return true;
    }

    @Override
    public int hashCode() {
        return Objects.hash(fullname, user_name, sdt, user_role, user_status);
    }

    @Override
    public String toString() {
        return "NguoiDungDTO{" +
                "fullname='" + fullname + '\'' +
                ", user_name='" + user_name + '\'' +
                ", sdt=" + sdt +
                ", user_role='" + user_role + '\'' +
                ", user_status='" + user_status + '\'' +
                '}';
    }
}
