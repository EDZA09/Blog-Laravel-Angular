import { Component, OnInit } from "@angular/core";
import { Router, ActivatedRoute, Params } from "@angular/router";
import { UserService } from "../../services/user.service";
import { CategoryService } from "../../services/category.service";
import { PostService } from "../../services/post.service";
import { Post } from "../../models/posts";
import { global } from "../../services/global";

@Component({
  selector: "app-post-new",
  templateUrl: "./post-new.component.html",
  styleUrls: ["./post-new.component.css"],
  providers: [UserService, CategoryService, PostService],
})
export class PostNewComponent implements OnInit {
  public page_title: string;
  public identity;
  public token;
  public post: Post;
  public categories;
  public status;
  //public url: string;

  public froala_options: Object = {
    charCounterCount: true,
    language: "es",
    toolbarButtons: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsXS: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsSM: ["bold", "italic", "underline", "paragraphFormat"],
    toolbarButtonsMD: ["bold", "italic", "underline", "paragraphFormat"],
  };

  public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg, .png, .gif, .jpeg",
    maxSize: "50",
    uploadAPI: {
      url: global.url + "post/upload",
      headers: {
        Authorization: this._userService.getToken(),
      },
    },
    //theme: "dragNDrop",
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    attachPinText: "Añade una Imagen a tu Entrada",
  };

  constructor(
    private _route: ActivatedRoute,
    private _router: Router,
    private _userService: UserService,
    private _categoryService: CategoryService,
    private _postService: PostService
  ) {
    this.page_title = "Crear una entrada";
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    //this.url = global.url;
  }

  ngOnInit() {
    this.getCategories();
    //console.log(this.identity);
    this.post = new Post(1, this.identity.sub, 1, "", "", null, null);
    //console.log(this.post);
  }

  getCategories() {
    this._categoryService.getCategories().subscribe(
      (response) => {
        if (response.status == "success") {
          this.categories = response.categories;
          //console.log(this.categories);
        }
      },
      (error) => {
        console.log(error);
      }
    );
  }

  imageUpload(data) {
    //console.log(JSON.parse(datos.response));
    let image_data = JSON.parse(data.response); //Conversión de JSON a Objeto
    this.post.image = image_data.image;
  }

  onSubmit(postNew) {
    //console.log(this.post);
    //console.log(this._postService.pruebas());
    this._postService.create(this.token, this.post).subscribe(
      (response) => {
        if (response.status == "success") {
          //console.log(response.post);
          this.post = response.post;
          //console.log("post: " + this.post);
          this.status = "success";
          this._router.navigate(["/inicio"]);
        }
      },
      (error) => {
        this.status = "error";
        console.log(error);
      }
    );
  }
}
