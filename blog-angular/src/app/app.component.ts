import { Component, OnInit, DoCheck } from "@angular/core";
import { UserService } from "./services/user.service";
import { CategoryService } from "./services/category.service";
import { global } from "./services/global";

@Component({
  selector: "app-root",
  templateUrl: "./app.component.html",
  styleUrls: ["./app.component.css"],
  providers: [UserService, CategoryService],
})
export class AppComponent implements OnInit, DoCheck {
  //title = "Blog de Angular v7.1.3";
  public title = "Blog de Angular";
  public identity;
  public token;
  public url;
  public categories;

  constructor(
    private _userService: UserService,
    private _categoriesService: CategoryService
  ) {
    this.loadUser();
    this.url = global.url;
  }

  ngOnInit() {
    console.log("Webapp cargada correctamente");
    this.getCategories();
  }

  ngDoCheck() {
    this.loadUser();
  }

  loadUser() {
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }

  getCategories() {
    this._categoriesService.getCategories().subscribe(
      (response) => {
        if (response.status == "success") {
          this.categories = response.categories;
          //console.log(this.categories); //verificando salida del listado de categorias
        }
      },
      (error) => {
        console.log(error);
      }
    );
  }
}
