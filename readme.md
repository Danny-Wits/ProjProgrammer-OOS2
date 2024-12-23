# API Documentation

## Overview

This API provides endpoints for managing users, their stats, friends, and targets, along with handling user authentication and leaderboard functionalities. It supports multiple HTTP methods such as `GET`, `POST`, `PUT`, and `DELETE` for various operations.

## Authentication

The API requires an API key for authentication in production environments. The key is passed in the `Authorization` header for all requests except when `$dev` is set to `true`.

## Base URL

The base URL for accessing the API depends on your server configuration. You can access the endpoints by appending the endpoint name to the base URL.

## Endpoints

### 1. GET Endpoints

#### `/leaderboard`

- **Method**: `GET`
- **Description**: Retrieves the leaderboard based on a specified category. The category is passed as a parameter in the request URL (e.g., `/leaderboard/{category}`).
- **Example Request**: `/leaderboard/default`
- **Response**: Returns the leaderboard data in JSON format for the specified category.

#### `/users/{id}`

- **Method**: `GET`
- **Description**: Retrieves stats and targets for a specific user identified by their user ID.
- **Parameters**:
  - `{id}` (required): The ID of the user.
- **Example Request**: `/users/123`
- **Response**: Returns the user’s data, including stats and targets.

### 2. POST Endpoints

#### `/register`

- **Method**: `POST`
- **Description**: Registers a new user with the provided details.
- **Body Parameters**:
  - `username` (required): The user's unique username.
  - `name` (required): The user's full name.
  - `email` (required): The user's email address.
  - `password` (required): The user's password.
  - `dateOfBirth` (required): The user's date of birth.
  - `location` (required): The user's location.
  - `bio` (required): The user's bio.
  - `profilePicture` (required): The user's profile picture.
  - `profession` (required): The user's profession.
- **Example Request**:

```json
{
  "username": "john_doe",
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "password123",
  "dateOfBirth": "1990-01-01",
  "location": "New York",
  "bio": "Software Developer",
  "profilePicture": "profile.jpg",
  "profession": "Developer"
}
```

#### `/login`

- **Method**: `POST`
- **Description**: Authenticates a user with their username and password.
- **Body Parameters**:
  - `username` (required): The user's username.
  - `password` (required): The user's password.
- **Example Request**:

```json
{
  "username": "john_doe",
  "password": "password123"
}
```

### 3. PUT Endpoints

#### `/users/{id}`

- **Method**: `PUT`
- **Description**: Updates a user's email, password, and other information.
- **Parameters**:
  - `{id}` (required): The ID of the user to be updated.
- **Body Parameters**:
  - `email` (required): The new email address.
  - `old_password` (required): The old password to verify before changing the password.
  - `password` (required): The new password.
- **Example Request**:

```json
{
  "email": "new.email@example.com",
  "old_password": "oldpassword123",
  "password": "newpassword123"
}
```

#### `/stats/{id}`

- **Method**: `PUT`
- **Description**: Updates the stats for a user.
- **Parameters**:
  - `{id}` (required): The ID of the user whose stats are being updated.
- **Body Parameters**:
  - `stats` (required): The new stats in JSON format.
- **Example Request**:

```json
{
  "stats": {
    "score": 1500,
    "level": 5
  }
}
```

#### `/friends/{id}`

- **Method**: `PUT`
- **Description**: Updates the friends list for a user.
- **Parameters**:
  - `{id}` (required): The ID of the user whose friends list is being updated.
- **Body Parameters**:
  - `friends` (required): A JSON list of friend IDs.
- **Example Request**:

```json
{
  "friends": [123, 456]
}
```

#### `/targets/{id}`

- **Method**: `PUT`
- **Description**: Updates the target for a user.
- **Parameters**:
  - `{id}` (required): The ID of the user whose target is being updated.
- **Body Parameters**:
  - `target` (required): The target in JSON format.
- **Example Request**:

```json
{
  "target": {
    "goal": "Reach level 10",
    "deadline": "2024-12-31"
  }
}
```

### 4. DELETE Endpoints

#### `/users/{id}`

- **Method**: `DELETE`
- **Description**: Deletes a user by their ID.
- **Parameters**:
  - `{id}` (required): The ID of the user to be deleted.
- **Example Request**: `/users/123`
- **Response**: Deletes the user and returns a success message.

---

## Error Handling

- The API returns errors in a JSON format with an appropriate HTTP status code.
- Common error responses:
  - `400 Bad Request`: Invalid or missing parameters.
  - `401 Unauthorized`: Authentication failed due to an incorrect API key.
  - `405 Method Not Allowed`: The requested method is not supported for the given endpoint.

## Allowed HTTP Methods

- `GET`: For retrieving data.
- `POST`: For creating new resources (e.g., user registration and login).
- `PUT`: For updating existing resources.
- `DELETE`: For deleting resources.

## Notes

- Always ensure that the API key is passed correctly in the Authorization header in production environments.
- Make sure to provide valid JSON data for endpoints that expect it in the request body.
  -@urbutt read this carefully
