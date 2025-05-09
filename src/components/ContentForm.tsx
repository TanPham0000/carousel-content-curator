
import React, { useState, ChangeEvent } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/components/ui/use-toast";
import { useContent } from "@/context/ContentContext";

const ContentForm: React.FC = () => {
  const [formData, setFormData] = useState({
    image: "",
    title: "",
    link: "",
    description: ""
  });
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const { addContent } = useContent();
  const { toast } = useToast();

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { id, value } = e.target;
    setFormData(prev => ({ ...prev, [id]: value }));
  };

  const handleImageChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setImageFile(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setFormData(prev => ({ ...prev, image: reader.result as string }));
      };
      reader.readAsDataURL(file);
    }
  };

  const validateForm = () => {
    const { image, title, link, description } = formData;
    
    if (!image) {
      toast({
        title: "Missing image",
        description: "Please upload an image for your content",
        variant: "destructive",
      });
      return false;
    }
    
    if (!title) {
      toast({
        title: "Missing title",
        description: "Please provide a title for your content",
        variant: "destructive",
      });
      return false;
    }
    
    if (!link) {
      toast({
        title: "Missing link",
        description: "Please provide a link to your article",
        variant: "destructive",
      });
      return false;
    }
    
    if (!link.startsWith("http://") && !link.startsWith("https://")) {
      toast({
        title: "Invalid link",
        description: "Please provide a valid URL starting with http:// or https://",
        variant: "destructive",
      });
      return false;
    }
    
    if (!description || description.length < 10) {
      toast({
        title: "Description too short",
        description: "Please provide a description with at least 10 characters",
        variant: "destructive",
      });
      return false;
    }
    
    return true;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    setIsSubmitting(true);
    addContent(formData);
    
    toast({
      description: "Content submitted successfully"
    });
    
    // Reset the form
    setFormData({ image: "", title: "", link: "", description: "" });
    setImageFile(null);
    setIsSubmitting(false);
  };

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle className="text-2xl text-center">Add New Content</CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <label htmlFor="image" className="block text-sm font-medium">Featured Image</label>
            <div className="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 bg-gray-50">
              {formData.image ? (
                <div className="relative">
                  <img 
                    src={formData.image} 
                    alt="Preview" 
                    className="mx-auto max-h-64 object-contain rounded-md"
                  />
                  <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    className="absolute top-2 right-2"
                    onClick={() => {
                      setFormData(prev => ({ ...prev, image: "" }));
                      setImageFile(null);
                    }}
                  >
                    Remove
                  </Button>
                </div>
              ) : (
                <>
                  <Input
                    id="image"
                    type="file"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="hidden"
                  />
                  <label 
                    htmlFor="image" 
                    className="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md cursor-pointer hover:bg-primary/90"
                  >
                    Select Image
                  </label>
                  <p className="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 10MB</p>
                </>
              )}
            </div>
          </div>
          
          <div className="space-y-2">
            <label htmlFor="title" className="block text-sm font-medium">Title</label>
            <Input
              id="title"
              placeholder="Enter content title"
              value={formData.title}
              onChange={handleInputChange}
            />
          </div>
          
          <div className="space-y-2">
            <label htmlFor="link" className="block text-sm font-medium">Article Link</label>
            <Input
              id="link"
              type="url"
              placeholder="https://example.com/your-article"
              value={formData.link}
              onChange={handleInputChange}
            />
          </div>
          
          <div className="space-y-2">
            <label htmlFor="description" className="block text-sm font-medium">Description</label>
            <Textarea
              id="description"
              placeholder="Write a brief description..."
              rows={3}
              value={formData.description}
              onChange={handleInputChange}
            />
          </div>
          
          <Button 
            type="submit" 
            className="w-full"
            disabled={isSubmitting}
          >
            {isSubmitting ? "Submitting..." : "Submit Content"}
          </Button>
        </form>
      </CardContent>
    </Card>
  );
};

export default ContentForm;
