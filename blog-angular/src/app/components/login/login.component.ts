import { Component, OnInit } from "@angular/core";
import { Router, ActivatedRoute, Params } from "@angular/router";
import { User } from "src/app/models/users";
import { UserService } from "src/app/services/user.service";

@Component({
  selector: "login",
  templateUrl: "./login.component.html",
  styleUrls: ["./login.component.css"],
  providers: [UserService],
})
export class LoginComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;
  public token;
  public identity;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute
  ) {
    this.page_title = "Identificate";
    this.user = new User(1, "", "", "ROLE_USER", "", "", "", "");
  }

  ngOnInit() {
    //Se ejecuta siempre y cierra sesión solo cuando se le da el parámetro sure por la url
    this.logout();
  }

  onSubmit(form) {
    //console.log(this.user);
    this._userService.signup(this.user).subscribe(
      (response) => {
        //RETORNA EL TOKEN
        //console.log(response);
        if (response.status != "error") {
          this.status = "success";
          this.token = response;

          //OBJETO DEL USUARIO IDENTIFICADO
          this._userService.signup(this.user, true).subscribe(
            (response) => {
              //RETORNA EL USUARIO
              //console.log(response);
              this.identity = response;

              console.log(this.token);
              console.log(this.identity);

              //PERSISTIR DATOS USUARIO IDENTIFICADO
              localStorage.setItem("token", this.token);
              localStorage.setItem("identity", JSON.stringify(this.identity));

              //Redireccion a la Página Inicial
              this._router.navigate(["inicio"]);
            },
            (error) => {
              this.status = "error";
              console.log(<any>error);
            }
          );
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

  logout() {
    this._route.params.subscribe((params) => {
      let logout = +params["sure"]; //el + convierte el array por un valor numérico. si este se coloca adenlante lo transforma en string

      if (logout == 1) {
        localStorage.removeItem("identity");
        localStorage.removeItem("token");
        this.identity = null;
        this.token = null;

        //Redireccion a la Página Inicial
        this._router.navigate(["inicio"]);
      }
    });
  }
}
