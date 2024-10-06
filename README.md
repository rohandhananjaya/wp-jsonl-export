# WP JSONL Export

WP JSONL Export is a WordPress plugin that allows you to export any post type in JSON Lines (JSONL) format. You can choose which post data (title, content, etc.) and metadata fields to include in the export, and customize the keys for each field.

## Features

- Export any post type to JSONL format.
- Select which post fields (title, content, excerpt, date, author) to include.
- Choose and include specific metadata fields.
- Customize the key names for each post data and metadata field.
- Simple and intuitive admin interface.

## Installation

1. **Download the Plugin**: Clone the repository or download the plugin as a ZIP file.

   ```bash
   git clone https://github.com/rohandhananjaya/wp-jsonl-export.git
   ```

2. **Upload the Plugin**:
    - Upload the `wp-jsonl-export` directory to the `/wp-content/plugins/` directory of your WordPress installation.

3. **Activate the Plugin**:
    - Go to the **Plugins** menu in WordPress.
    - Activate the **WP JSONL Export** plugin.

## Usage

1. **Navigate to WP JSONL Export**:
    - Go to the **Tools > WP JSONL Export** in your WordPress dashboard.

2. **Select a Post Type**:
    - Choose the post type you want to export.

3. **Choose Fields to Include**:
    - Check the boxes for post data fields you want to include (e.g., title, content, etc.).
    - Edit the key names for these fields if needed.

4. **Select Metadata** (optional):
    - If you want to include metadata, check the option and select specific metadata fields.
    - You can also edit the metadata key names.

5. **Export**:
    - Click on the "Export to JSONL" button to download the file.

## Example Output

If you rename "Title" to "Heading" and "Content" to "Body," the exported JSONL file might look like this:

```json
{
    "heading": "Post Title Here",
    "body": "The content of the post goes here...",
    "metadata": {
        "custom_field_key": "Custom Field Value"
    }
}
```

## Development

If you wish to contribute to this plugin, here are some steps to get started:

1. **Fork the Project**: Create your own fork of the repository by clicking the "Fork" button on the top-right of this repository page.
2. **Clone Your Fork**: Clone the forked repository to your local machine.
   
   ```bash
   git clone https://github.com/<your-username>/wp-jsonl-export.git
   ```

3. **Make Your Changes**: Create a new branch for your feature or bugfix.

   ```bash
   git checkout -b my-feature-branch
   ```

4. **Commit and Push Your Changes**:

   ```bash
   git add .
   git commit -m "Added my feature"
   git push origin my-feature-branch
   ```

5. **Submit a Pull Request**: Once your changes are ready, submit a pull request to the main repository.

## Contributing

Contributions are welcome! Whether it's reporting a bug, suggesting a new feature, or submitting a pull request, all contributions help improve this project.

To get started:

- Fork the repository.
- Create your branch.
- Submit a pull request with detailed descriptions of your changes.

Please follow [WordPress coding standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) when contributing.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

- **Rohan Dhananjaya** - [rohandhananjaya](https://github.com/rohandhananjaya)