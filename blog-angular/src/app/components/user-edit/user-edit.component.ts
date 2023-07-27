import { Component, OnInit } from "@angular/core";
import { Observable } from "rxjs";
import { User } from "src/app/models/users";
import { UserService } from "src/app/services/user.service";
import { global } from "src/app/services/global";

@Component({
  selector: "app-user-edit",
  templateUrl: "./user-edit.component.html",
  styleUrls: ["./user-edit.component.css"],
  providers: [UserService],
})
export class UserEditComponent implements OnInit {
  public page_title: string;
  public user: User;
  public identity;
  public token;
  public status: string;
  public url;

  public froala_options: Object = {
    charCounterCount: true,
    language: "es",
    toolbarButtons: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsXS: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsSM: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsMD: ["bold", "italic", "underline", "paragraphFormat"],
  };
  /*public afuConfig = {
    uploadAPI: {
      url: "https://example-file-upload-api",
    },
  };*/
  public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg, .png, .gif, .jpeg",
    maxSize: "50",
    uploadAPI: {
      url: global.url + "user/upload",
      headers: {
        Authorization: this._userService.getToken(),
      },
    },
    //theme: "dragNDrop",
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    attachPinText: "Sube tu avatar de usuario",
  };

  constructor(private _userService: UserService) {
    this.page_title = "Ajustes de Usuario";
    this.user = new User(1, "", "", "ROLE_USER", "", "", "", "");
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.url = global.url;

    //Rellenar objeto de usuario
    //this.user = this.identity;
    this.user = new User(
      this.identity.sub,
      this.identity.name,
      this.identity.surname,
      this.identity.role,
      this.identity.email,
      "",
      this.identity.description,
      this.identity.image
    );
    //this.user.sub = null;
  }

  ngOnInit() {
    //Comprobando que el link para obtener imagen se correcto
    //console.log(this.url + "user/avatar/" + this.identity.image);
  }

  onSubmit(form) {
    this._userService.update(this.token, this.user).subscribe(
      (response) => {
        //console.log(response);

        if (response && response.status) {
          console.log(response);
          this.status = "success";

          //Actualizar usuario en sesión
          if (response.changes.name) {
            this.user.name = response.changes.name;
          }

          if (response.changes.surname) {
            this.user.surname = response.changes.surname;
          }

          if (response.changes.email) {
            this.user.email = response.changes.email;
          }

          if (response.changes.description) {
            this.user.description = response.changes.description;
          }

          if (response.changes.image) {
            this.user.image = response.changes.image;
          }

          //this.identity = response.user;
          this.identity = this.user;
          localStorage.setItem("identity", JSON.stringify(this.identity));
        } else {
          this.status = "error";
        }
      },
      (error) => {
        this.status = "error";
        console.log(<any>error);
      }
    );
  }

  avatarUpload(datos) {
    //console.log(JSON.parse(datos.response));
    let data = JSON.parse(datos.response); //Conversión de JSON a Objeto
    this.user.image = data.image;
  }
}
