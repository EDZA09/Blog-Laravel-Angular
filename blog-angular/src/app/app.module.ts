import { BrowserModule } from "@angular/platform-browser";
import { NgModule } from "@angular/core";
import { routing, appRoutingProviders } from "./app.routing";
import { FormsModule } from "@angular/forms";
//import { HttpClient, HttpHeaders } from "@angular/common/http";
import { HttpClientModule } from "@angular/common/http";
// Import Angular plugin. Texto Enriquecido
import { FroalaEditorModule, FroalaViewModule } from "angular-froala-wysiwyg";
import { AngularFileUploaderModule } from "angular-file-uploader";

import { AppComponent } from "./app.component";
import { LoginComponent } from "./components/login/login.component";
import { RegisterComponent } from "./components/register/register.component";
import { from } from "rxjs";
import { HomeComponent } from "./components/home/home.component";
import { ErrorComponent } from "./components/error/error.component";
import { UserEditComponent } from "./components/user-edit/user-edit.component";
import { CategoryNewComponent } from "./components/category-new/category-new.component";
import { PostNewComponent } from "./components/post-new/post-new.component";
import { PostDetailComponent } from "./components/post-detail/post-detail.component";
import { PostEditComponent } from "./components/post-edit/post-edit.component";
import { CategoryDetailComponent } from "./components/category-detail/category-detail.component";

import { UserService } from "./services/user.service";
import { IdentityGuard } from "./services/identity.guard";
import { ProfileComponent } from './components/profile/profile.component';
import { PostListComponent } from './components/post-list/post-list.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    HomeComponent,
    ErrorComponent,
    UserEditComponent,
    CategoryNewComponent,
    PostNewComponent,
    PostDetailComponent,
    PostEditComponent,
    CategoryDetailComponent,
    ProfileComponent,
    PostListComponent,
  ],
  imports: [
    BrowserModule,
    routing,
    FormsModule,
    HttpClientModule,
    FroalaEditorModule.forRoot(),
    FroalaViewModule.forRoot(),
    AngularFileUploaderModule,
  ],
  providers: [appRoutingProviders, IdentityGuard, UserService],
  bootstrap: [AppComponent],
})
export class AppModule {}
