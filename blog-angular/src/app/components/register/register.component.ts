import { ClassGetter } from "@angular/compiler/src/output/output_ast";
import { Component, OnInit } from "@angular/core";
import { User } from "src/app/models/users";
import { UserService } from "src/app/services/user.service";

@Component({
  selector: "register",
  templateUrl: "./register.component.html",
  styleUrls: ["./register.component.css"],
  providers: [UserService],
})
export class RegisterComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;

  constructor(private _userService: UserService) {
    this.page_title = "Registrate";
    this.user = new User(1, "", "", "ROLE_USER", "", "", "", "");
  }

  ngOnInit() {
    console.log("Componente de registro Lanzado!!");
  }

  onSubmit(form) {
    /*
    console.log(this.user);
    console.log(this._userService.test);*/
    this._userService.register(this.user).subscribe(
      (response) => {
        if (response.status == "success") {
          this.status = response.status;
          console.log(response);
          form.reset();
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
}
