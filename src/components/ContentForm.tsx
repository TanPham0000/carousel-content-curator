
import React, { useState, ChangeEvent } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/components/ui/use-toast";
import { useContent } from "@/context/ContentContext";

const ContentForm: React.FC = () => {
  const [image, setImage] = useState<string>("");
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [link, setLink] = useState<string>("");
  const [description, setDescription] = useState<string>("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const { addContent } = useContent();
  const { toast } = useToast();

  const handleImageChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setImageFile(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImage(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  const validateForm = () => {
    if (!image) {
      toast({
        title: "Missing image",
        description: "Please upload an image for your content",
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
    
    // In a real app, we would upload the image to a server here
    // For now, we'll just use the data URL
    addContent({
      image,
      link,
      description,
    });
    
    toast({
      title: "Content submitted successfully",
      description: "Your content has been added to the carousel",
    });
    
    // Reset the form
    setImage("");
    setImageFile(null);
    setLink("");
    setDescription("");
    setIsSubmitting(false);
  };

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <CardTitle className="text-2xl font-bold text-center">Add New Content</CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="space-y-2">
            <label htmlFor="image" className="block text-sm font-medium">
              Featured Image
            </label>
            <div className="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-gray-400 bg-gray-50">
              {image ? (
                <div className="relative w-full">
                  <img 
                    src={image} 
                    alt="Preview" 
                    className="mx-auto max-h-64 object-contain rounded-md"
                  />
                  <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    className="absolute top-2 right-2"
                    onClick={() => {
                      setImage("");
                      setImageFile(null);
                    }}
                  >
                    Remove
                  </Button>
                </div>
              ) : (
                <div className="text-center">
                  <div className="mt-2">
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
                    <p className="text-xs text-gray-500 mt-2">
                      PNG, JPG, GIF up to 10MB
                    </p>
                  </div>
                </div>
              )}
            </div>
          </div>
          
          <div className="space-y-2">
            <label htmlFor="link" className="block text-sm font-medium">
              Article Link
            </label>
            <Input
              id="link"
              type="url"
              placeholder="https://example.com/your-article"
              value={link}
              onChange={(e) => setLink(e.target.value)}
            />
          </div>
          
          <div className="space-y-2">
            <label htmlFor="description" className="block text-sm font-medium">
              Description
            </label>
            <Textarea
              id="description"
              placeholder="Write a brief description of your content..."
              rows={4}
              value={description}
              onChange={(e) => setDescription(e.target.value)}
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
