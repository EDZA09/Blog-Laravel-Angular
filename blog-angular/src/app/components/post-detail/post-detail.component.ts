import { Component, OnInit } from "@angular/core";
import { ActivatedRoute, Router, Params } from "@angular/router";
import { UserService } from "../../services/user.service";
import { PostService } from "../../services/post.service";
import { Post } from "../../models/posts";
import { global } from "../../services/global";

@Component({
  selector: "app-post-detail",
  templateUrl: "./post-detail.component.html",
  styleUrls: ["./post-detail.component.css"],
  providers: [PostService, UserService],
})
export class PostDetailComponent implements OnInit {
  public page_title: string;
  public post: Post;
  public identity;
  public url = global.url;

  constructor(
    private _route: ActivatedRoute,
    private _router: Router,
    private _postService: PostService,
    private _userService: UserService
  ) {
    this.identity = this._userService.getIdentity();
  }

  ngOnInit() {
    this.getPost();
  }

  getPost() {
    // Sacar el id del post de la ruta
    this._route.params.subscribe((params) => {
      let id = +params["id"];
      //console.log(id);

      // Petición ajax para obtener los datos del post
      this._postService.getPost(id).subscribe(
        (response) => {
          if (response.status == "success") {
            this.post = response.posts;
            console.log(this.post);
          } else {
            this._router.navigate(["/inicio"]);
          }
        },
        (error) => {
          console.log(error);
          this._router.navigate(["/inicio"]);
        }
      );
    });
  }
}
